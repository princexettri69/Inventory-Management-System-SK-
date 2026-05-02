<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
    <!-- Header & Selectors -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-foreground">Ledger Book (Khata)</h1>
            <p class="text-muted-foreground">Track financial history for specific customers or suppliers.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <!-- Entity Type Toggle -->
            <div class="flex items-center bg-card p-1 rounded-lg border border-border shadow-sm">
                <button wire:click="$set('type', 'customer')" class="px-4 py-1.5 rounded-md text-sm font-medium transition-all {{ $type === 'customer' ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-muted' }}">
                    Customer
                </button>
                <button wire:click="$set('type', 'supplier')" class="px-4 py-1.5 rounded-md text-sm font-medium transition-all {{ $type === 'supplier' ? 'bg-primary text-primary-foreground shadow-sm' : 'text-muted-foreground hover:bg-muted' }}">
                    Supplier
                </button>
            </div>

            <!-- Entity Selector -->
            <div class="flex items-center gap-2 bg-card px-3 py-2 rounded-lg border border-border shadow-sm min-w-[200px]">
                <x-heroicon-o-user class="w-5 h-5 text-muted-foreground" />
                <select wire:model.live="entity_id" class="border-none focus:ring-0 bg-transparent text-sm font-medium cursor-pointer w-full">
                    <option value="">Select {{ ucfirst($type) }}</option>
                    @foreach($entities as $entity)
                        <option value="{{ $entity->id }}">{{ $entity->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date Range -->
            <div class="flex items-center gap-2 bg-card px-3 py-2 rounded-lg border border-border shadow-sm">
                <x-heroicon-o-calendar class="w-5 h-5 text-muted-foreground" />
                <input type="date" wire:model.live="start_date" class="border-none focus:ring-0 bg-transparent text-sm font-medium cursor-pointer" />
                <span class="text-muted-foreground">to</span>
                <input type="date" wire:model.live="end_date" class="border-none focus:ring-0 bg-transparent text-sm font-medium cursor-pointer" />
            </div>
        </div>
    </div>

    @if(!$entity_id)
        <div class="bg-card border border-border rounded-xl p-12 text-center shadow-sm">
            <div class="w-20 h-20 bg-muted rounded-full flex items-center justify-center mx-auto mb-4">
                <x-heroicon-o-book-open class="w-10 h-10 text-muted-foreground" />
            </div>
            <h3 class="text-lg font-bold">Select an entity to view ledger</h3>
            <p class="text-muted-foreground">Choose a customer or supplier from the dropdown above to see their transaction history.</p>
        </div>
    @else
        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-card p-6 rounded-xl border border-border shadow-sm relative overflow-hidden group">
                <p class="text-sm font-medium text-muted-foreground uppercase tracking-wider mb-1">Opening Balance</p>
                <h2 class="text-2xl font-bold text-foreground" wire:loading.class="opacity-50 transition-opacity">
                    <span wire:loading.remove>{{ format_money($openingBalance) }}</span>
                    <span wire:loading>...</span>
                </h2>
                <p class="mt-2 text-xs text-muted-foreground italic">Balance before {{ \Carbon\Carbon::parse($start_date)->format('M d, Y') }}</p>
            </div>

            <div class="bg-card p-6 rounded-xl border border-emerald-100 shadow-sm relative overflow-hidden group">
                <p class="text-sm font-medium text-emerald-600 uppercase tracking-wider mb-1">Total Debit (+)</p>
                <h2 class="text-2xl font-bold text-foreground" wire:loading.class="opacity-50 transition-opacity">
                    <span wire:loading.remove>{{ format_money($totalDebit) }}</span>
                    <span wire:loading>...</span>
                </h2>
                <div class="mt-4">
                    <button wire:click="openModal('income')" wire:loading.attr="disabled" class="text-[10px] bg-emerald-600 text-white px-2 py-1 rounded-full hover:bg-emerald-700 transition-colors flex items-center gap-1">
                        <x-heroicon-o-plus class="w-3 h-3" /> Add Debit
                    </button>
                </div>
            </div>

            <div class="bg-card p-6 rounded-xl border border-red-100 shadow-sm relative overflow-hidden group">
                <p class="text-sm font-medium text-red-600 uppercase tracking-wider mb-1">Total Credit (-)</p>
                <h2 class="text-2xl font-bold text-foreground" wire:loading.class="opacity-50 transition-opacity">
                    <span wire:loading.remove>{{ format_money($totalCredit) }}</span>
                    <span wire:loading>...</span>
                </h2>
                <div class="mt-4">
                    <button wire:click="openModal('expense')" wire:loading.attr="disabled" class="text-[10px] bg-red-600 text-white px-2 py-1 rounded-full hover:bg-red-700 transition-colors flex items-center gap-1">
                        <x-heroicon-o-plus class="w-3 h-3" /> Add Credit
                    </button>
                </div>
            </div>

            <div class="bg-card p-6 rounded-xl border {{ $closingBalance >= 0 ? 'border-emerald-200' : 'border-red-200' }} shadow-sm relative overflow-hidden group">
                <p class="text-sm font-medium {{ $closingBalance >= 0 ? 'text-emerald-600' : 'text-red-600' }} uppercase tracking-wider mb-1">Closing Balance</p>
                <h2 class="text-2xl font-bold text-foreground" wire:loading.class="opacity-50 transition-opacity">
                    <span wire:loading.remove>{{ format_money($closingBalance) }}</span>
                    <span wire:loading>...</span>
                </h2>
                <p class="mt-2 text-xs text-muted-foreground italic">Balance as of {{ \Carbon\Carbon::parse($end_date)->format('M d, Y') }}</p>
            </div>
        </div>

        <!-- Ledger Table -->
        <div class="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-muted/50 border-b border-border">
                        <tr>
                            <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider">Description</th>
                            <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider">Ref/Invoice</th>
                            <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider text-right">Debit</th>
                            <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider text-right">Credit</th>
                            <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider text-right">Balance</th>
                            <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <!-- Opening Balance Row -->
                        <tr class="bg-muted/10 italic">
                            <td class="px-6 py-4 text-sm">{{ \Carbon\Carbon::parse($start_date)->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 text-sm font-bold" colspan="2">Opening Balance Forward</td>
                            <td class="px-6 py-4 text-sm text-right">-</td>
                            <td class="px-6 py-4 text-sm text-right">-</td>
                            <td class="px-6 py-4 text-sm font-bold text-right">{{ format_money($openingBalance) }}</td>
                            <td class="px-6 py-4 text-sm text-right">-</td>
                        </tr>

                        @php $runningBalance = $openingBalance; @endphp
                        @forelse($transactions as $tx)
                            @php
                                if ($type === 'customer') {
                                    $debit = $tx->category->type === \App\Enums\FinanceCategoryType::Income ? $tx->amount : 0;
                                    $credit = $tx->category->type === \App\Enums\FinanceCategoryType::Expense ? $tx->amount : 0;
                                } else {
                                    $credit = $tx->category->type === \App\Enums\FinanceCategoryType::Expense ? $tx->amount : 0;
                                    $debit = $tx->category->type === \App\Enums\FinanceCategoryType::Income ? $tx->amount : 0;
                                }
                                $runningBalance += ($debit - $credit);
                            @endphp
                            <tr class="hover:bg-muted/30 transition-colors group">
                                <td class="px-6 py-4 text-sm text-muted-foreground">{{ $tx->transaction_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="text-sm font-medium text-foreground">{{ $tx->description }}</div>
                                        @if($tx->reference_type)
                                            <span class="text-[8px] bg-blue-50 text-blue-600 px-1 py-0.5 rounded border border-blue-100 uppercase font-bold">Auto</span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] text-muted-foreground uppercase">{{ $tx->category->name }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm font-mono text-muted-foreground">{{ $tx->external_reference ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-right font-bold text-emerald-600">
                                    {{ $debit > 0 ? format_money($debit) : '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-bold text-red-600">
                                    {{ $credit > 0 ? format_money($credit) : '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-bold text-foreground">
                                    {{ format_money($runningBalance) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-right">
                                    @if(!$tx->reference_type)
                                        <button wire:click="deleteEntry({{ $tx->id }})" wire:confirm="Delete this manual entry?" class="text-red-500 hover:text-red-700 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <x-heroicon-o-trash class="w-4 h-4 ml-auto" />
                                        </button>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-muted-foreground italic">
                                    No transactions found for the selected period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-muted/50 border-t border-border font-bold">
                        <tr>
                            <td class="px-6 py-4 text-sm" colspan="3">Totals for period</td>
                            <td class="px-6 py-4 text-sm text-right text-emerald-600">{{ format_money($totalDebit) }}</td>
                            <td class="px-6 py-4 text-sm text-right text-red-600">{{ format_money($totalCredit) }}</td>
                            <td class="px-6 py-4 text-sm text-right">{{ format_money($closingBalance) }}</td>
                            <td class="px-6 py-4 text-sm text-right"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Print Action -->
        <div class="mt-8 flex justify-end">
            <button onclick="window.print()" class="flex items-center gap-2 bg-primary text-primary-foreground px-6 py-3 rounded-xl shadow-lg hover:shadow-xl transition-all font-bold">
                <x-heroicon-o-printer class="w-5 h-5" />
                Print Statement
            </button>
        </div>
    @endif

    <!-- Manual Entry Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-background/80 backdrop-blur-sm">
        <div class="bg-card border border-border rounded-xl shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-4 border-b border-border flex justify-between items-center {{ $entryType === 'income' ? 'bg-emerald-50' : 'bg-red-50' }}">
                <h3 class="font-bold {{ $entryType === 'income' ? 'text-emerald-800' : 'text-red-800' }} flex items-center gap-2">
                    <x-heroicon-o-bolt class="w-5 h-5" />
                    Manual Ledger Entry ({{ ucfirst($entryType) }})
                </h3>
                <button wire:click="$set('showModal', false)" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
            <form wire:submit.prevent="saveEntry" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Date</label>
                    <input type="date" wire:model="transaction_date" class="w-full border-border bg-background rounded-md focus:ring-primary" />
                    @error('transaction_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Category</label>
                    <select wire:model="category_id" class="w-full border-border bg-background rounded-md focus:ring-primary">
                        <option value="">Select Category</option>
                        @foreach($this->categories as $cat)
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
                    <textarea wire:model="description" class="w-full border-border bg-background rounded-md focus:ring-primary" rows="2" placeholder="Payment received, Advanced, etc."></textarea>
                    @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Reference (Optional)</label>
                    <input type="text" wire:model="reference" class="w-full border-border bg-background rounded-md focus:ring-primary" placeholder="E.g. Receipt No, Cheque No" />
                    @error('reference') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="button" wire:click="$set('showModal', false)" class="flex-1 px-4 py-2 border border-border rounded-md hover:bg-muted transition-colors font-medium">Cancel</button>
                    <button type="submit" wire:loading.attr="disabled" class="flex-1 px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors font-bold shadow-sm flex items-center justify-center gap-2">
                        <span wire:loading wire:target="saveEntry" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                        <span wire:loading.remove wire:target="saveEntry">Save Entry</span>
                        <span wire:loading wire:target="saveEntry">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
