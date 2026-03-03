<?php

namespace App\Immonet\Immowelt;

use App\Immonet\Exceptions\ImmonetException;
use Illuminate\Support\Facades\Storage;

class Client
{
    protected string $ftpHost;

    protected int $ftpPort;

    protected string $ftpUsername;

    protected string $ftpPassword;

    protected string $ftpPath;

    protected bool $ftpSsl;

    public function __construct(
        ?string $ftpHost = null,
        ?int $ftpPort = null,
        ?string $ftpUsername = null,
        ?string $ftpPassword = null,
        ?string $ftpPath = null,
        ?bool $ftpSsl = null,
    ) {
        $this->ftpHost = $ftpHost ?? config('services.immowelt.ftp_host', '');
        $this->ftpPort = $ftpPort ?? (int) config('services.immowelt.ftp_port', 21);
        $this->ftpUsername = $ftpUsername ?? config('services.immowelt.ftp_username', '');
        $this->ftpPassword = $ftpPassword ?? config('services.immowelt.ftp_password', '');
        $this->ftpPath = $ftpPath ?? config('services.immowelt.ftp_path', '/');
        $this->ftpSsl = $ftpSsl ?? (bool) config('services.immowelt.ftp_ssl', false);
    }

    /**
     * Upload an OpenImmo ZIP file to Immowelt FTP server.
     *
     * @throws ImmonetException
     */
    public function upload(string $zipFilePath, ?string $remoteFilename = null): void
    {
        if (! file_exists($zipFilePath)) {
            throw new ImmonetException("ZIP file not found: {$zipFilePath}");
        }

        $remoteFilename = $remoteFilename ?? basename($zipFilePath);
        $remotePath = rtrim($this->ftpPath, '/').'/'.$remoteFilename;

        $connection = $this->connect();

        try {
            $result = ftp_put($connection, $remotePath, $zipFilePath, FTP_BINARY);

            if (! $result) {
                throw new ImmonetException("Failed to upload file to FTP: {$remotePath}");
            }
        } finally {
            ftp_close($connection);
        }
    }

    /**
     * Upload OpenImmo XML content directly.
     *
     * @param  array<string, string>  $images  Array of image paths (local file paths)
     *
     * @throws ImmonetException
     */
    public function uploadOpenImmo(string $xmlContent, array $images = [], string $filename = 'openimmo.zip'): void
    {
        $tempDir = sys_get_temp_dir().'/immowelt_'.uniqid();
        $zipPath = $tempDir.'/'.$filename;

        try {
            mkdir($tempDir, 0755, true);

            // Create ZIP archive
            $zip = new \ZipArchive;
            if ($zip->open($zipPath, \ZipArchive::CREATE) !== true) {
                throw new ImmonetException("Could not create ZIP archive: {$zipPath}");
            }

            // Add XML file
            $zip->addFromString('openimmo.xml', $xmlContent);

            // Add images
            foreach ($images as $imagePath) {
                if (Storage::exists($imagePath)) {
                    $content = Storage::get($imagePath);
                    if ($content !== null) {
                        $zip->addFromString(basename($imagePath), $content);
                    }
                } elseif (file_exists($imagePath)) {
                    $zip->addFile($imagePath, basename($imagePath));
                }
            }

            $zip->close();

            // Upload to FTP
            $this->upload($zipPath);
        } finally {
            // Cleanup temp files
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
            if (is_dir($tempDir)) {
                rmdir($tempDir);
            }
        }
    }

    /**
     * Delete a file from the FTP server.
     *
     * @throws ImmonetException
     */
    public function delete(string $remoteFilename): void
    {
        $remotePath = rtrim($this->ftpPath, '/').'/'.$remoteFilename;
        $connection = $this->connect();

        try {
            $result = ftp_delete($connection, $remotePath);

            if (! $result) {
                throw new ImmonetException("Failed to delete file from FTP: {$remotePath}");
            }
        } finally {
            ftp_close($connection);
        }
    }

    /**
     * List files on the FTP server.
     *
     * @return array<string>
     *
     * @throws ImmonetException
     */
    public function listFiles(?string $path = null): array
    {
        $path = $path ?? $this->ftpPath;
        $connection = $this->connect();

        try {
            $files = ftp_nlist($connection, $path);

            if ($files === false) {
                throw new ImmonetException("Failed to list files from FTP: {$path}");
            }

            return $files;
        } finally {
            ftp_close($connection);
        }
    }

    /**
     * Connect to the FTP server.
     *
     * @return resource
     *
     * @throws ImmonetException
     */
    protected function connect()
    {
        if ($this->ftpSsl) {
            $connection = ftp_ssl_connect($this->ftpHost, $this->ftpPort);
        } else {
            $connection = ftp_connect($this->ftpHost, $this->ftpPort);
        }

        if (! $connection) {
            throw new ImmonetException("Could not connect to FTP server: {$this->ftpHost}:{$this->ftpPort}");
        }

        $loginResult = ftp_login($connection, $this->ftpUsername, $this->ftpPassword);

        if (! $loginResult) {
            ftp_close($connection);
            throw new ImmonetException("FTP login failed for user: {$this->ftpUsername}");
        }

        // Enable passive mode (required for most modern FTP servers)
        ftp_pasv($connection, true);

        return $connection;
    }

    /**
     * Check if the client is configured.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->ftpHost)
            && ! empty($this->ftpUsername)
            && ! empty($this->ftpPassword);
    }

    /**
     * Set FTP host.
     */
    public function setFtpHost(string $host): self
    {
        $this->ftpHost = $host;

        return $this;
    }

    /**
     * Get FTP host.
     */
    public function getFtpHost(): string
    {
        return $this->ftpHost;
    }
}
