<div class="col-12 mt-4">
    <div class="row pricesBox">
        @foreach($wallets as $wallet)

        <?php
        $transWallet = \App\Models\Transaction::where('wallet', $wallet->id)->where('transaction_type','service')->get();
        $sumType = 0;
        foreach($transWallet as $data)
        {
            $date = date('Y-m-d', strtotime($data->creationTime));
            $dateMonth = date('m', strtotime($data->creationTime));
            if(request('filter_by_date') == 'today')
            {
                if($date == $today)
                {
                    $sumType +=$data->amount;
                }
            }
            elseif(request('filter_by_date') == 'yesterday')
            {
                if($date == $yesterday)
                {
                    $sumType +=$data->amount;
                }
            }
            elseif(request('filter_by_date') == 'last_7_days')
            {
                if($date >= $last_7_days && $date <= $today)
                {
                    $sumType +=$data->amount;
                }
            }
            elseif(request('filter_by_date') == 'last_30_days')
            {
                if($date >= $last_30_days && $date <= $today)
                {
                    $sumType +=$data->amount;
                }
            }
            elseif(request('filter_by_date') == 'last_month')
            {
                if($dateMonth == $last_month)
                {
                    $sumType +=$data->amount;
                }
            }
            elseif(request('filter_by_date') == 'this_month')
            {
                if($dateMonth == $this_month)
                {
                    $sumType +=$data->amount;
                }
            }
            else
            {
                if($date == $today)
                {
                    $sumType +=$data->amount;
                }
            }
        }

        ?>
        <div class="col-6 mb-3">{{$wallet->wallet_name}}</div>
        <div class="col-6 mb-3">{{$sumType}} {{$wallet->wallet_currency}}</div>
        @endforeach
    </div>
</div>