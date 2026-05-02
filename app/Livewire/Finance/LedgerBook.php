<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\FinanceTransaction;
use App\Enums\FinanceCategoryType;
use Carbon\Carbon;
use Livewire\Attributes\Url;

class LedgerBook extends Component
{
    #[Url]
    public $type = 'customer'; // 'customer' or 'supplier'

    #[Url]
    public $entity_id;

    #[Url]
    public $start_date;

    #[Url]
    public $end_date;

    public $transactions = [];
    public $openingBalance = 0;
    public $totalDebit = 0;
    public $totalCredit = 0;
    public $closingBalance = 0;

    // Manual Entry Form
    public $showModal = false;
    public $entryType = 'income'; // 'income' or 'expense'
    public $category_id;
    public $amount;
    public $description;
    public $reference;
    public $transaction_date;

    public function mount()
    {
        if (!$this->start_date) {
            $this->start_date = now()->startOfMonth()->format('Y-m-d');
        }
        if (!$this->end_date) {
            $this->end_date = now()->format('Y-m-d');
        }
        $this->transaction_date = now()->format('Y-m-d');
        
        if ($this->entity_id) {
            $this->loadData();
        }
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['type', 'entity_id', 'start_date', 'end_date'])) {
            $this->loadData();
        }
    }

    public function openModal($entryType = 'income')
    {
        if (!$this->entity_id) {
            $this->dispatch('toast', message: 'Please select an entity first.', type: 'warning');
            return;
        }
        $this->entryType = $entryType;
        $this->reset(['category_id', 'amount', 'description', 'reference']);
        $this->transaction_date = now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function saveEntry(\App\Services\FinanceTransactionService $service)
    {
        $this->validate([
            'category_id' => 'required|exists:finance_categories,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'reference' => 'nullable|string|max:50',
            'transaction_date' => 'required|date',
        ]);

        $data = new \App\DTOs\FinanceTransactionData(
            transaction_date: Carbon::parse($this->transaction_date),
            finance_category_id: $this->category_id,
            amount: (int) $this->amount,
            description: $this->description,
            external_reference: $this->reference,
            created_by: \Illuminate\Support\Facades\Auth::id(),
            customer_id: $this->type === 'customer' ? $this->entity_id : null,
            supplier_id: $this->type === 'supplier' ? $this->entity_id : null,
        );

        try {
            $service->createTransaction($data);
            $this->showModal = false;
            $this->loadData();
            $this->dispatch('toast', message: 'Ledger entry recorded successfully!', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Error: ' . $e->getMessage(), type: 'error');
        }
    }

    public function deleteEntry($id, \App\Services\FinanceTransactionService $service)
    {
        $transaction = FinanceTransaction::findOrFail($id);
        
        try {
            $service->deleteTransaction($transaction);
            $this->loadData();
            $this->dispatch('toast', message: 'Entry deleted.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        }
    }

    public function loadData()
    {
        if (!$this->entity_id) {
            $this->transactions = [];
            return;
        }

        $query = FinanceTransaction::with('category')
            ->where($this->type . '_id', $this->entity_id);

        // Opening Balance calculation
        $openingQuery = clone $query;
        $this->openingBalance = $this->calculateBalance(
            $openingQuery->where('transaction_date', '<', $this->start_date)->get()
        );

        // Transactions in range
        $this->transactions = $query->whereBetween('transaction_date', [$this->start_date, $this->end_date])
            ->orderBy('transaction_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $this->totalDebit = 0;
        $this->totalCredit = 0;

        foreach ($this->transactions as $t) {
            if ($this->type === 'customer') {
                // For customer: Income is Credit (they paid us), Expense/Sale is Debit (they owe us)
                // Actually, let's simplify: 
                // Sale (Income) = Debit (Increase what they owe)
                // Payment (Expense? No, if they pay us it's Income for us but reduces their debt)
                
                // standard accounting for Customer:
                // Debit = Sales, Credit = Payments
                if ($t->category->type === FinanceCategoryType::Income) {
                    $this->totalDebit += $t->amount;
                } else {
                    $this->totalCredit += $t->amount;
                }
            } else {
                // For supplier: Purchase (Expense) = Credit (Increase what we owe), Payment = Debit
                if ($t->category->type === FinanceCategoryType::Expense) {
                    $this->totalCredit += $t->amount;
                } else {
                    $this->totalDebit += $t->amount;
                }
            }
        }

        $this->closingBalance = $this->openingBalance + ($this->totalDebit - $this->totalCredit);
    }

    private function calculateBalance($transactions)
    {
        $debit = 0;
        $credit = 0;
        foreach ($transactions as $t) {
            if ($this->type === 'customer') {
                if ($t->category->type === FinanceCategoryType::Income) {
                    $debit += $t->amount;
                } else {
                    $credit += $t->amount;
                }
            } else {
                if ($t->category->type === FinanceCategoryType::Expense) {
                    $credit += $t->amount;
                } else {
                    $debit += $t->amount;
                }
            }
        }
        return $debit - $credit;
    }

    public function getEntitiesProperty()
    {
        return $this->type === 'customer' 
            ? Customer::orderBy('name')->get() 
            : Supplier::orderBy('name')->get();
    }

    public function getCategoriesProperty()
    {
        return \App\Models\FinanceCategory::where('type', $this->entryType)->get();
    }

    public function render()
    {
        return view('livewire.finance.ledger-book', [
            'entities' => $this->entities
        ])->layout('layouts.app', ['title' => 'Ledger Book']);
    }
}
