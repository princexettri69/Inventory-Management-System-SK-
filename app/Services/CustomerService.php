<?php

namespace App\Services;

use Exception;
use App\Models\Customer;
use App\DTOs\CustomerData;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomerException;

class CustomerService
{
    /**
     * Create a new customer record.
     */
    public function createCustomer(CustomerData $data): Customer
    {
        return DB::transaction(function () use ($data) {
            try {
                return Customer::create([
                    'name' => $data->name,
                    'email' => $data->email,
                    'phone' => $data->phone,
                    'pan_number' => $data->pan_number,
                    'address' => $data->address,
                    'notes' => $data->notes,
                ]);

            } catch (Exception $e) {
                throw CustomerException::creationFailed($e->getMessage(), [
                    'data' => (array) $data,
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        });
    }

    /**
     * Update an existing customer record.
     */
    public function updateCustomer(Customer $customer, CustomerData $data): Customer
    {
        return DB::transaction(function () use ($customer, $data) {
            try {
                $customer->update([
                    'name' => $data->name,
                    'email' => $data->email,
                    'phone' => $data->phone,
                    'pan_number' => $data->pan_number,
                    'address' => $data->address,
                    'notes' => $data->notes,
                ]);

                return $customer->refresh();

            } catch (Exception $e) {
                throw CustomerException::updateFailed($e->getMessage(), [
                    'id' => $customer->id,
                    'data' => (array) $data,
                ]);
            }
        });
    }

    /**
     * Delete a customer record.
     */
    public function deleteCustomer(Customer $customer): void
    {
        DB::transaction(function () use ($customer) {
            try {
                $customer->delete();

            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() === '23000') {
                    throw CustomerException::deletionFailed('Cannot delete this customer because they have associated sales or transactions.', ['id' => $customer->id]);
                }
                throw CustomerException::deletionFailed($e->getMessage(), ['id' => $customer->id]);
            } catch (Exception $e) {
                throw CustomerException::deletionFailed($e->getMessage(), ['id' => $customer->id]);
            }
        });
    }
}
