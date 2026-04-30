<?php

namespace App\DTOs;

class CustomerData
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $email,
        public readonly ?string $phone,
        public readonly ?string $pan_number,
        public readonly ?string $address,
        public readonly ?string $notes,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: empty($data['email']) ? null : $data['email'],
            phone: empty($data['phone']) ? null : $data['phone'],
            pan_number: empty($data['pan_number']) ? null : $data['pan_number'],
            address: empty($data['address']) ? null : $data['address'],
            notes: empty($data['notes']) ? null : $data['notes'],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'pan_number' => $this->pan_number,
            'address' => $this->address,
            'notes' => $this->notes,
        ];
    }
}
