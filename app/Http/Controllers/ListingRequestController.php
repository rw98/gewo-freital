<?php

namespace App\Http\Controllers;

use App\Enums\ListingRequestStatus;
use App\Models\ListingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ListingRequestController extends Controller
{
    /**
     * Verify the requestee's email address via signed URL.
     */
    public function verifyEmail(Request $request, ListingRequest $listingRequest): RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            abort(403, __('Der Bestätigungslink ist ungültig oder abgelaufen.'));
        }

        if ($listingRequest->email_confirmed_at !== null) {
            return redirect()
                ->route('listing-requests.portal', $listingRequest->access_token)
                ->with('info', __('listing_requests.email_confirmed'));
        }

        if ($listingRequest->status !== ListingRequestStatus::PendingEmailConfirmation) {
            return redirect()
                ->route('listing-requests.portal', $listingRequest->access_token)
                ->with('info', __('listing_requests.email_confirmed'));
        }

        $listingRequest->update([
            'email_confirmed_at' => now(),
            'status' => ListingRequestStatus::Confirmed,
        ]);

        return redirect()
            ->route('listing-requests.portal', $listingRequest->access_token)
            ->with('success', __('listing_requests.email_confirmed'));
    }
}
