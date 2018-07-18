<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bill;
use Auth;

use PayPal\Api\Amount;
use Session;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;

use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class PaymentController extends Controller
{

    private $apiContext;
    public function __construct(){
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                config('paypal.client_id'),
                config('paypal.secret')
            )
        );
        $this->apiContext->setConfig(config('paypal.settings'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $payment_id = Session::get('payment_id');  //lay session id tra ve
        Session::forget('payment_id');  //xoa session
        //dd($request->all());
        $execution = new PaymentExecution();
        $execution->setPayerID($request->input('PayerID'));
        $payment = Payment::get($payment_id, $this->apiContext);

        try{
            $result = $payment->execute($execution, $this->apiContext);

            // Paypal transactions
            $transaction = $payment->getTransactions()[0];
            $itemlist = $transaction->getItemList();
            $item = $itemlist->getItems()[0];
            $name = $item->getName();

            $idbill="";
            for($i=13; $i<strlen($name) ;$i++)
            {
                $idbill .= $name[$i];
                if($name[$i] == ' ') break;
            }

            //dd($result);
            if($result->getState() == 'approved'){
                $bill = Bill::find($idbill);
                $bill->tinhtrangdon = 3;
                $bill->save();

                return redirect()->route('lich-su')->with('thanhcongTT','Thanh toan thanh cong');
            }
            return "Thanh toan that bai";
        }catch(Exception $e){
            return "Failed";
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($this->apiContext);

        //
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");
        // ### Itemized information
        // (Optional) Lets you specify item wise
        // information
        $item1 = new Item();
        $item1->setName('ID don tour: '.$request->idbill." Ten tour: ".$request->tentour)
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice(($request->tongtien)/22500);
        
        $itemList = new ItemList();
        $itemList->setItems(array($item1));
        // ### Additional payment details
        // Use this optional field to set additional
        // payment information such as tax, shipping
        // charges etc.
        // $details = new Details();
        // $details->setShipping(1.2)
        //     ->setTax(1.3)
        //     ->setSubtotal(17.50);
        // ### Amount
        // Lets you specify a payment amount.
        // You can also specify additional details
        // such as shipping, tax.
        $amount = new Amount();
        $amount->setCurrency("USD")
            ->setTotal(($request->tongtien)/22500);
        //    ->setDetails($details);
        // ### Transaction
        // A transaction defines the contract of a
        // payment - what is the payment for and who
        // is fulfilling it. 
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription("Payment description")
            ->setInvoiceNumber(uniqid());
        // ### Redirect urls
        // Set the urls that the buyer must be redirected to after 
        // payment approval/ cancellation.
        ////$baseUrl = getBaseUrl();
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(route('payment.create'))
            ->setCancelUrl(route('payment.create'));
        // ### Payment
        // A Payment Resource; create one using
        // the above types and intent set to 'sale'
        $payment = new Payment();
        $payment->setIntent("authorize")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));
        // For Sample Purposes Only.
        ////$request = clone $payment;
        // ### Create Payment
        // Create a payment by calling the 'create' method
        // passing it a valid apiContext.
        // (See bootstrap.php for more on `ApiContext`)
        // The return object contains the state and the
        // url to which the buyer must be redirected to
        // for payment approval
        try {
            $payment->create($this->apiContext);
        } catch (Exception $ex) {
            // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
            //// ResultPrinter::printError("Created Payment Authorization Using PayPal. Please visit the URL to Authorize.", "Payment", null, $request, $ex);
            echo "Faild";
            exit(1);
        }
        // ### Get redirect url
        // The API response provides the url that you must redirect
        // the buyer to. Retrieve the url from the $payment->getLinks()
        // method
        $approvalUrl = $payment->getApprovalLink();
        // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
        //// ResultPrinter::printResult("Created Payment Authorization Using PayPal. Please visit the URL to Authorize.", "Payment", "<a href='$approvalUrl' >$approvalUrl</a>", $request, $payment);

        ////echo "<pre>";
        ////return $payment;
        Session::put('payment_id',$payment->id);
        return redirect()->to($approvalUrl);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
