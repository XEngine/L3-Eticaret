<?php

Class SanalPos{

	protected $factory;

	const CONNECTOR_TYPE = Connector::CONNECTOR_TYPE_HTTP;
  
    protected $transactionMap = array(
        self::TRANSACTION_TYPE_PREAUTHORIZATION => 'PreAuth',
        self::TRANSACTION_TYPE_POSTAUTHORIZATION => 'PostAuth',
        self::TRANSACTION_TYPE_SALE => 'Auth',
        self::TRANSACTION_TYPE_CANCEL => 'Void',
        self::TRANSACTION_TYPE_REFUND => 'Credit',
        self::TRANSACTION_TYPE_POINT_QUERY => '',
        self::TRANSACTION_TYPE_POINT_USAGE => '',
    );

	function __construct($string){
		$this->factory = Config::get("sanalpos.factory.".$string);
		$this->InitializeAdapter($this->factory->type);
	}

	public function InitializeAdapter($type){
		/*Adapter Selection*/
		$AdapterClass = "\\VirtualPos\\Adapters\\{$type}";
		
		if (! class_exists($AdapterClass)) {
            throw new UnknownAdapter(
                'Bilinmeyen ödeme yöntemi : ' . $AdapterClass
            );
        }
        return new $AdapterClass( $type );
	}

	public function makeProvision(){

	}
}