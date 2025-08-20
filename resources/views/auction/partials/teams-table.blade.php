<!-- Teams Table Section -->
<div class="px-4 py-4 sm:px-6 border-b glacier-border">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold glacier-text-primary">League Teams & Balances</h2>
        <div class="badge-primary px-3 py-1 rounded-full text-sm font-medium">
            6 Teams
        </div>
    </div>
</div>
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="table-header">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium glacier-text-secondary uppercase tracking-wider">
                    Team
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium glacier-text-secondary uppercase tracking-wider">
                    Wallet Balance
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200/50" id="teamsTableBody">
            <!-- Static Team Data -->
            <tr class="table-row hover:bg-gray-50/70 transition-colors duration-200 team-row" 
                data-team-id="1" 
                data-wallet="500000">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-full w-10 h-10 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-lg">
                            MI
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium glacier-text-primary">Mumbai Indians</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-green-600" id="teamSpent_1">
                        ₹125,000
                    </div>
                    <div class="text-sm text-red-500">₹375,000</div>
                </td>
            </tr>

            <tr class="table-row hover:bg-gray-50/70 transition-colors duration-200 team-row" 
                data-team-id="2" 
                data-wallet="450000">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-full w-10 h-10 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-lg">
                            CSK
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium glacier-text-primary">Chennai Super Kings</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-green-600" id="teamSpent_2">
                        ₹160,000
                    </div>
                    <div class="text-sm text-red-500">₹290,000</div>
                </td>
            </tr>

            <tr class="table-row hover:bg-gray-50/70 transition-colors duration-200 team-row" 
                data-team-id="3" 
                data-wallet="400000">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-full w-10 h-10 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-lg">
                            RCB
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium glacier-text-primary">Royal Challengers Bangalore</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-green-600" id="teamSpent_3">
                        ₹95,000
                    </div>
                    <div class="text-sm text-red-500">₹305,000</div>
                </td>
            </tr>

            <tr class="table-row hover:bg-gray-50/70 transition-colors duration-200 team-row" 
                data-team-id="4" 
                data-wallet="350000">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-full w-10 h-10 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-lg">
                            KKR
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium glacier-text-primary">Kolkata Knight Riders</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-green-600" id="teamSpent_4">
                        ₹85,000
                    </div>
                    <div class="text-sm text-red-500">₹265,000</div>
                </td>
            </tr>

            <tr class="table-row hover:bg-gray-50/70 transition-colors duration-200 team-row" 
                data-team-id="5" 
                data-wallet="300000">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-full w-10 h-10 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-lg">
                            DC
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium glacier-text-primary">Delhi Capitals</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-green-600" id="teamSpent_5">
                        ₹70,000
                    </div>
                    <div class="text-sm text-red-500">₹230,000</div>
                </td>
            </tr>

            <tr class="table-row hover:bg-gray-50/70 transition-colors duration-200 team-row" 
                data-team-id="6" 
                data-wallet="250000">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-full w-10 h-10 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-lg">
                            PBKS
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium glacier-text-primary">Punjab Kings</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-green-600" id="teamSpent_6">
                        ₹55,000
                    </div>
                    <div class="text-sm text-red-500">₹195,000</div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

@if(auth()->user()->isOrganizer())
<!-- Summary Row -->
<div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div>
            <div class="text-gray-500">Total Teams</div>
            <div class="font-medium text-gray-900">6</div>
        </div>
        <div>
            <div class="text-gray-500">Total Wallet</div>
            <div class="font-medium text-green-600">₹2,250,000</div>
        </div>
        <div>
            <div class="text-gray-500">Total Spent</div>
            <div class="font-medium text-red-600">₹590,000</div>
        </div>
        <div>
            <div class="text-gray-500">Avg Balance</div>
            <div class="font-medium text-blue-600">₹375,000</div>
        </div>
    </div>
</div>
@endif`