<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Api\Payment\Services\EcpayServices;
use App\Api\Log\Traits\LogTraits;

class ecpay extends Command
{
    use LogTraits;
    protected $ecpayServices;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crontab:ecpay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[排程] 檢查綠界訂單';


    /**
     * Create a new command instance.
     * @Inject
     * @param $ecpayServices
     *
     * @return void
     */
    public function __construct(EcpayServices $ecpayServices) {
        parent::__construct();
        $this->ecpayServices = $ecpayServices;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 檢查綠界已過期的訂單(ATM、超商支付)
        $result = $this->ecpayServices->checkExpire();
        if ($result['result'] == false) {
            $this->createLog('ecpayExpire',[$result['error']]);
        }

        // 刪除暫存的訂單 [要問大J] 只能刪募資平台的
        /*$result = $this->ecpayServices->deleteOrder();
        if ($result['result'] == false) {
            $this->createLog('ecpayTempDel',[$result['error']]);
        }*/

        $this->info('ecpay is done');
    }
}
