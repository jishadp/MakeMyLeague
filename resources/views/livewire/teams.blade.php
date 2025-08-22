<div class="glacier-card">
                <div class="px-4 py-4 sm:px-6 border-b glacier-border">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold glacier-text-primary">Teams</h2>
                        <div class="badge-blue px-3 py-1 rounded-full text-sm font-medium">
                            8 Teams
                        </div>
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="space-y-3">
                        @foreach ($teams as $team)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                                    MI
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $team->team->name}}</p>
                                    <p class="text-sm text-gray-500">₹{{$team->wallet_balance}} remaining</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-green-600">{{$team->leaguePlayers->count()}} Players</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
