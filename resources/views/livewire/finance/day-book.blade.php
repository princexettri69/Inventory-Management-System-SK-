<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
    <!-- Header & Date Selector -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-foreground">Digital Day Book</h1>
            <p class="text-muted-foreground">Manage your daily income and expenses professionally.</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="print" class="flex items-center gap-2 bg-white text-foreground px-4 py-2 rounded-lg border border-border shadow-sm hover:bg-muted transition-colors font-medium">
                <x-heroicon-o-printer class="w-5 h-5 text-muted-foreground" />
                Print Day Book
            </button>
            <div class="flex items-center gap-2 bg-card p-2 rounded-lg border border-border shadow-sm">
                <x-heroicon-o-calendar class="w-5 h-5 text-muted-foreground" />
                <input type="date" wire:model.live="date" class="border-none focus:ring-0 bg-transparent text-sm font-medium cursor-pointer" />
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-card p-6 rounded-xl border border-emerald-100 shadow-sm relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                <x-heroicon-o-arrow-trending-up class="w-16 h-16 text-emerald-600" />
            </div>
            <p class="text-sm font-medium text-emerald-600 uppercase tracking-wider mb-1">Total Income</p>
            <h2 class="text-3xl font-bold text-foreground">{{ format_money($totalIncome) }}</h2>
            <div class="mt-4 flex items-center gap-2">
                <button wire:click="openQuickEntry('income')" class="text-xs bg-emerald-600 text-white px-3 py-1.5 rounded-full hover:bg-emerald-700 transition-colors flex items-center gap-1">
                    <x-heroicon-o-plus class="w-3 h-3" /> Add Income
                </button>
            </div>
        </div>

        <div class="bg-card p-6 rounded-xl border border-red-100 shadow-sm relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                <x-heroicon-o-arrow-trending-down class="w-16 h-16 text-red-600" />
            </div>
            <p class="text-sm font-medium text-red-600 uppercase tracking-wider mb-1">Total Expense</p>
            <h2 class="text-3xl font-bold text-foreground">{{ format_money($totalExpense) }}</h2>
            <div class="mt-4 flex items-center gap-2">
                <button wire:click="openQuickEntry('expense')" class="text-xs bg-red-600 text-white px-3 py-1.5 rounded-full hover:bg-red-700 transition-colors flex items-center gap-1">
                    <x-heroicon-o-plus class="w-3 h-3" /> Add Expense
                </button>
            </div>
        </div>

        <div class="bg-card p-6 rounded-xl border border-border shadow-sm relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                <x-heroicon-o-banknotes class="w-16 h-16 text-primary" />
            </div>
            <p class="text-sm font-medium text-muted-foreground uppercase tracking-wider mb-1">Net Balance</p>
            <h2 class="text-3xl font-bold {{ $netBalance >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                {{ format_money($netBalance) }}
            </h2>
            <p class="mt-4 text-xs text-muted-foreground italic">Balance for {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</p>
        </div>
    </div>

    <!-- Ledger View -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Income Ledger -->
        <div class="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
            <div class="bg-emerald-50/50 px-6 py-4 border-b border-emerald-100 flex justify-between items-center">
                <h3 class="font-bold text-emerald-800 flex items-center gap-2">
                    <x-heroicon-o-arrow-down-circle class="w-5 h-5" />
                    Income Ledger
                </h3>
                <span class="text-xs font-semibold bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full uppercase">{{ $incomeTransactions->count() }} Entries</span>
            </div>
            <div class="divide-y divide-border max-h-[600px] overflow-y-auto">
                @forelse($incomeTransactions as $tx)
                    <div class="px-6 py-4 flex justify-between items-start hover:bg-muted/30 transition-colors group">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-foreground">{{ $tx->category->name }}</span>
                                @if($tx->reference_type)
                                    <span class="text-[10px] bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded border border-blue-100 uppercase font-bold">Auto</span>
                                @endif
                            </div>
                            <p class="text-xs text-muted-foreground mt-1">{{ $tx->description }}</p>
                            @if($tx->external_reference)
                                <p class="text-[10px] text-muted-foreground mt-1 font-mono bg-muted inline-block px-1 rounded">Ref: {{ $tx->external_reference }}</p>
                            @endif
                        </div>
                        <div class="text-right flex flex-col items-end gap-2">
                            <span class="text-sm font-bold text-emerald-600">+ {{ format_money($tx->amount) }}</span>
                            @if(!$tx->reference_type)
                                <button wire:click="deleteEntry({{ $tx->id }})" wire:confirm="Are you sure you want to delete this entry?" class="opacity-0 group-hover:opacity-100 transition-opacity text-red-500 hover:text-red-700">
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center text-muted-foreground italic">
                        No income recorded for this day.
                    </div>
                @endforelse
            </div>
            @if($incomeTransactions->count() > 0)
            <div class="bg-muted/20 px-6 py-3 border-t border-border flex justify-between items-center font-bold">
                <span>Total Income</span>
                <span class="text-emerald-600">{{ format_money($totalIncome) }}</span>
            </div>
            @endif
        </div>

        <!-- Expense Ledger -->
        <div class="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
            <div class="bg-red-50/50 px-6 py-4 border-b border-red-100 flex justify-between items-center">
                <h3 class="font-bold text-red-800 flex items-center gap-2">
                    <x-heroicon-o-arrow-up-circle class="w-5 h-5" />
                    Expense Ledger
                </h3>
                <span class="text-xs font-semibold bg-red-100 text-red-700 px-2 py-0.5 rounded-full uppercase">{{ $expenseTransactions->count() }} Entries</span>
            </div>
            <div class="divide-y divide-border max-h-[600px] overflow-y-auto">
                @forelse($expenseTransactions as $tx)
                    <div class="px-6 py-4 flex justify-between items-start hover:bg-muted/30 transition-colors group">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-foreground">{{ $tx->category->name }}</span>
                                @if($tx->reference_type)
                                    <span class="text-[10px] bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded border border-blue-100 uppercase font-bold">Auto</span>
                                @endif
                            </div>
                            <p class="text-xs text-muted-foreground mt-1">{{ $tx->description }}</p>
                            @if($tx->external_reference)
                                <p class="text-[10px] text-muted-foreground mt-1 font-mono bg-muted inline-block px-1 rounded">Ref: {{ $tx->external_reference }}</p>
                            @endif
                        </div>
                        <div class="text-right flex flex-col items-end gap-2">
                            <span class="text-sm font-bold text-red-600">- {{ format_money($tx->amount) }}</span>
                            @if(!$tx->reference_type)
                                <button wire:click="deleteEntry({{ $tx->id }})" wire:confirm="Are you sure you want to delete this entry?" class="opacity-0 group-hover:opacity-100 transition-opacity text-red-500 hover:text-red-700">
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center text-muted-foreground italic">
                        No expenses recorded for this day.
                    </div>
                @endforelse
            </div>
            @if($expenseTransactions->count() > 0)
            <div class="bg-muted/20 px-6 py-3 border-t border-border flex justify-between items-center font-bold">
                <span>Total Expense</span>
                <span class="text-red-600">{{ format_money($totalExpense) }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Quick Entry Modal -->
    @if($showQuickEntry)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-background/80 backdrop-blur-sm">
        <div class="bg-card border border-border rounded-xl shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-4 border-b border-border flex justify-between items-center {{ $entryType === 'income' ? 'bg-emerald-50' : 'bg-red-50' }}">
                <h3 class="font-bold {{ $entryType === 'income' ? 'text-emerald-800' : 'text-red-800' }} flex items-center gap-2">
                    <x-heroicon-o-bolt class="w-5 h-5" />
                    Quick Add {{ ucfirst($entryType) }}
                </h3>
                <button wire:click="$set('showQuickEntry', false)" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
            <form wire:submit.prevent="saveEntry" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Category</label>
                    <select wire:model="category_id" class="w-full border-border bg-background rounded-md focus:ring-primary">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Amount</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-muted-foreground font-medium">{{ \App\Models\Setting::get('currency_symbol', 'Rp') }}</span>
                        <input type="number" wire:model="amount" class="w-full pl-10 border-border bg-background rounded-md focus:ring-primary" placeholder="0.00" />
                    </div>
                    @error('amount') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Description</label>
                    <textarea wire:model="description" class="w-full border-border bg-background rounded-md focus:ring-primary" rows="2" placeholder="What is this for?"></textarea>
                    @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Reference (Optional)</label>
                    <input type="text" wire:model="reference" class="w-full border-border bg-background rounded-md focus:ring-primary" placeholder="E.g. Bill No, Cheque No" />
                    @error('reference') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="button" wire:click="$set('showQuickEntry', false)" class="flex-1 px-4 py-2 border border-border rounded-md hover:bg-muted transition-colors font-medium">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors font-bold shadow-sm">Save Entry</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
