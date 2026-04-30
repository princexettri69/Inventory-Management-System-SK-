<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\FinanceTransaction;
use App\Models\FinanceCategory;
use App\Enums\FinanceCategoryType;
use App\Services\FinanceTransactionService;
use App\DTOs\FinanceTransactionData;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Livewire\Attributes\Url;

class DayBook extends Component
{
    #[Url]
    public $date;

    public $incomeTransactions = [];
    public $expenseTransactions = [];
    public $totalIncome = 0;
    public $totalExpense = 0;
    public $netBalance = 0;

    // Form fields for quick entry
    public $showQuickEntry = false;
    public $entryType = 'income'; // 'income' or 'expense'
    public $category_id;
    public $amount;
    public $description;
    public $reference;

    protected $listeners = ['refreshDayBook' => '$refresh'];

    public function mount()
    {
        if (!$this->date) {
            $this->date = now()->format('Y-m-d');
        }
        $this->loadData();
    }

    public function updatedDate()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $transactions = FinanceTransaction::with('category')
            ->whereDate('transaction_date', $this->date)
            ->get();

        $this->incomeTransactions = $transactions->filter(fn($t) => $t->category->type === FinanceCategoryType::Income);
        $this->expenseTransactions = $transactions->filter(fn($t) => $t->category->type === FinanceCategoryType::Expense);

        $this->totalIncome = $this->incomeTransactions->sum('amount');
        $this->totalExpense = $this->expenseTransactions->sum('amount');
        $this->netBalance = $this->totalIncome - $this->totalExpense;
    }

    public function openQuickEntry($type)
    {
        $this->entryType = $type;
        $this->showQuickEntry = true;
        $this->reset(['category_id', 'amount', 'description', 'reference']);
    }

    public function saveEntry(FinanceTransactionService $service)
    {
        $this->validate([
            'category_id' => 'required|exists:finance_categories,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'reference' => 'nullable|string|max:50',
        ]);

        $data = new FinanceTransactionData(
            transaction_date: Carbon::parse($this->date),
            finance_category_id: $this->category_id,
            amount: (int) $this->amount,
            description: $this->description,
            external_reference: $this->reference,
            created_by: Auth::id()
        );

        try {
            $service->createTransaction($data);
            $this->showQuickEntry = false;
            $this->loadData();
            $this->dispatch('toast', message: 'Entry recorded successfully!', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Error: ' . $e->getMessage(), type: 'error');
        }
    }

    public function print()
    {
        $transactions = FinanceTransaction::whereDate('transaction_date', $this->date)->pluck('id')->toArray();

        if (empty($transactions)) {
            $this->dispatch('toast', message: 'No transactions to print for this day.', type: 'warning');
            return;
        }

        // Generate a unique ID for this print session
        $printId = (string) \Illuminate\Support\Str::uuid();

        // Store selected IDs in cache for 5 minutes
        \Illuminate\Support\Facades\Cache::put("finance_print_{$printId}", $transactions, now()->addMinutes(5));

        // Construct URL
        $url = route('finance.transactions.print', ['printId' => $printId]) . '?period=today';

        $this->dispatch('open-print-window', url: $url);
    }

    public function getCategoriesProperty()
    {
        return FinanceCategory::where('type', $this->entryType)->get();
    }

    public function deleteEntry($id, FinanceTransactionService $service)
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

    public function render()
    {
        return view('livewire.finance.day-book', [
            'categories' => $this->categories
        ])->layout('layouts.app', ['title' => 'Day Book']);
    }
}
