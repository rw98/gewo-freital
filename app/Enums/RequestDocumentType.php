<?php

namespace App\Enums;

enum RequestDocumentType: string
{
    case IncomeProof = 'income_proof';
    case IdDocument = 'id_document';
    case EmploymentContract = 'employment_contract';
    case Schufa = 'schufa';
    case RentalHistory = 'rental_history';
    case Other = 'other';

    public function label(): string
    {
        return __('enums.request_document_type.'.$this->value);
    }
}
