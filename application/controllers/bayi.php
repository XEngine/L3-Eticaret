<?php

class Bayi_Controller extends Base_Controller {
	public $restful = true;

	public function get_index(){
		return View::make('bayi.index');
	}
	public function post_getbank(){
		$data = Input::all();
		$ccBin = substr($data['cc'], 0, 6);

		$Information = Binlist::with('getBankInformation')->where('bin','=',$ccBin)->first();

		return Response::eloquent($Information); 
	}
	public function post_payment(){
		$data = Input::all();

		$config  = json_decode(file_get_contents('application/config/sanalpos.json'));

		if(empty($data['bnkinfo'])){
			$data['bnkinfo'] = "ingbank";
		}

		$adapter = Paranoia\Payment\Factory::createInstance($config, $data['bnkinfo']);
		$request = new Paranoia\Payment\Request();
		$request->setCardNumber($data['cc'])
		        ->setSecurityCode($data['cvv'])
		        ->setExpireMonth($data['mm'])
		        ->setExpireYear('20'.$data['yy'])
		        ->setOrderId('ORDER000000' . time())
		        ->setAmount($data['price'])
		        ->setCurrency('TRY');
		try {
		    $response = $adapter->sale($request);
		   	print_r($response);
		} catch( Paranoia\Communication\Exception\CommunicationFailed $e) {
		    print "Baglanti saglanamadi." . PHP_EOL;
		} catch(Paranoia\Payment\Exception\ UnexpectedResponse $e) {
		    print "Banka beklenmedik bir cevap dondu." . PHP_EOL;
		} catch(Exception $e) {
		    print "Beklenmeyen bir hata olustu." . PHP_EOL;
		}
		return View::make('bayi.paymentresult')->with('response',$response);
	}
	public function get_payment(){
		return View::make('bayi.payment');
	}
	public function get_login(){
		return View::make('bayi.login');
	}
}