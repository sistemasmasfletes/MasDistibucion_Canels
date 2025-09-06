<?php

/**
 * @Entity(repositoryClass="DefaultDb_Repositories_UserRepository")
 * @Table(name="users")
 */
class DefaultDb_Entities_User
{
    /* constantes para tipo de usuario */
    const USER_ADMIN = 1;
    const USER_DRIVER = 2;
    const USER_CLIENT = 3;
    const USER_SECRETARY = 4;
    const USER_CLIENT_MAS_DISTRIBUCION = 5;
    const USER_STORER = 6; // almacenista
    const USER_OPERATION_CONTROLLER = 7; // controlador de operaciones
    const USER_CEO = 8;

    /* constantes para tipo de usuario */
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_BLOCK = 2;
    
    /* constantes para el dia que desea tener el usuario para facturar */
    const DAY_MONDAY = 1;
    const DAY_TUESDAY = 2;
    const DAY_WEDNESDAY = 3;
    const DAY_THURSDAY = 4;
    const DAY_FRIDAY = 5;
    const DAY_SATURDAR = 6;
    const DAY_SUNDAY = 7;
    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

    /**
     * @Column(type="string", name="code")
     * @var string
     */
    protected $code;

    /**
     * @Column(type="integer", name="status" , options={"default" = 1})
     * @var integer
     */
    protected $status = 1;
    
    /**
     * @Column(type="string", name="first_name")
     * @var string
     */
    protected $firstName;

    /**
     * @Column(type="string", name="last_name")
     * @var string
     */
    protected $lastName;

    /**
     * @Column(type="string", name="username")
     * @var string
     */
    protected $username;

    /**
     * @Column(type="string", name="password")
     * @var string
     */
    protected $password;

    /**
     * @Column(type="string", name="title", nullable=true)
     * @var string
     */
    protected $title;

    /**
     * @Column(type="string", name="commercial_name", nullable=true)
     * @var string
     */
    protected $commercialName;

    /**
     * @Column(type="string", name="cell_phone", nullable=true)
     * @var string
     */
    protected $cellPhone;
    
    /**
     * @Column(type="string", name="local_number", nullable=true)
     * @var string
     */
    protected $localNumber;

    /**
     * @Column(type="string", name="type", nullable=true)
     * @var string
     */
    protected $type;

    /**
     * @OneToMany(targetEntity="DefaultDb_Entities_Catalog", mappedBy="client")
     * @OrderBy({"title" = "ASC"})
     * */
    private $catalogs;

    /**
     * @OneToMany(targetEntity="DefaultDb_Entities_M3CommerceOrder", mappedBy="seller")
     * @OrderBy({"id" = "DESC"})
     * */
    private $ordersReceived;

    /**
     * @OneToMany(targetEntity="DefaultDb_Entities_M3CommerceOrder", mappedBy="buyer")
     * @OrderBy({"id" = "DESC"})
     * */
    private $ordersGenerated;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Category")
     */
    protected $category;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Point")
     */
    protected $point = 0;
    
    /**
     * @Column(type="integer", name="dayInvoice")
     * @var integer
     */
    protected $dayInvoice = 0;
    
    /**
     * @OneToMany(targetEntity="DefaultDb_Entities_BranchesUser", mappedBy="client")
     **/
    private $branches;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     */
    protected $parent = null;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Role")
     */
    protected $role;
    
    /**
     * @Column(type="float", name="credito", nullable=true)
     * @var float
     */
    protected $credito;
    
     /**
     * @Column(type="float", name="numCreditoCongelado", nullable=true)
     * @var float
     */
    protected $creditoCongelado;
    
    /**
     * @Column(type="float", name="numCreditoNegativo", nullable=true)
     * @var float
     */
    protected $creditoNegativo;
    
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_TipoMonedas")
     */
    protected $moneda;
    
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_State")
     * @JoinColumn(nullable=true)
     */
    protected $state;
    
     /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Paises")
     * @JoinColumn(referencedColumnName="id", nullable=true)
     */
    protected $country;
    
     /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_City")
     * @JoinColumn(nullable=true)
     */
    protected $city;
    
    /** @Column(type="string", name="terms_acp", options={"default" = "0"})
     * @var string
     */
    private $terms;
    
    /** @Column(type="string", name="service_acp", options={"default" = "0"})
     * @var string
     */
    private $service;
    
    /** @Column(type="string", name="privacy_acp", options={"default" = "0"})
     * @var string
     */
    private $privacy;
    
    /** @Column(type="string", name="rfc")
     * @var string
     */
    private $rfc;
    
    /** @Column(type="string", name="street")
     * @var string
     */
    private $street;
    
    /** @Column(type="string", name="suburb")
     * @var string
     */
    private $suburb;
    
    /** @Column(type="string", name="number")
     * @var string
     */
    private $number;
    
    /** @Column(type="string", name="numint")
     * @var string
     */
    private $numint;
    
    /** @Column(type="string", name="zip")
     * @var string
     */
    private $zip;
    
    /** @Column(type="string", name="visible", options={"default" = "0"})
     * @var string
     */
    private $visible;
    
    /** @Column(type="string", name="name_bank")
     * @var string
     */
    private $bank;
    
    /** @Column(type="string", name="bank_account")
     * @var string
     */
    private $account;
    
    /** @Column(type="string", name="clabe")
     * @var string
     */
    private $clabe;
    
    /** @Column(type="string", name="company_mail")
     * @var string
     */
    private $mail;
    
    /** @Column(type="string", name="company_phone")
     * @var string
     */
    private $phone;
    
    /** @Column(type="string", name="crd_pay", options={"default" = "0"})
     * @var string
     */
    private $crdpay;
    
    /** @Column(type="string", name="dely_pay", options={"default" = "0"})
     * @var string
     */
    private $delypay;

    /** @Column(type="string", name="business_name")
     * @var string
     */
    private $businessname;
    
    /** @Column
     * @var string
     */
    private $token;
    
    /** @Column
     * @var string
     */
    private $msgtoclients;

    /** @Column
     * @var string
     */
     private $msgtoclients2;    
    
    /** @Column
     * @var string
     */
    private $registration;	
	
    /** @Column
     * @var string
     */
    private $linktoclients;
	
	public function getLink(){
		return $this->linktoclients;
	}
	
	public function setLink($linktoclients){
		$this->linktoclients = $linktoclients;
	}	

	public function getRegistration(){
		return $this->registration;
	}
	
	public function setRegistration($registration){
		$this->registration = $registration;
	}
	
    public function getId()
    {
        return $this->id;
    }
   
    public function getStatus()
    {
        return $this->status;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }
    
    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getCommercialName()
    {
        return $this->commercialName;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getCellPhone()
    {
        return $this->cellPhone;
    }

    public function getLocalNumber()
    {
        return $this->localNumber;
    }

    public function getCategory()
    {
        return $this->category;
    }
    
    public function getMoneda()
    {
        return $this->moneda;
    }

    public function getDayInvoice()
    {
        return $this->dayInvoice;
    }
    
    public function getBranches()
    {
        return $this->branches;
    }

    public function getParent()
    {
        return $this->parent;
    }
    
    public function getCredito()
    {
        return $this->credito;
    }
    public function getCreditoCongelado()
    {
        return $this->creditoCongelado;
    }
    public function getCreditoNegativo()
    {
        return $this->creditoNegativo;
    }
    
    public function getState(){
        return $this->state;
    }
    
    public function getCountry(){
        return $this->country;
    } 
    
    public function getCity(){
        return $this->city;
    }
    

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setCommercialName($commercialName)
    {
        $this->commercialName = $commercialName;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setCellPhone($cellPhone)
    {
        $this->cellPhone = $cellPhone;
    }

    public function setLocalNumber($localNumber)
    {
        $this->localNumber = $localNumber;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }
    
    public function setMoneda($moneda)
    {
        $this->moneda = $moneda;
    }

    public function setPoint($point)
    {
        $this->point = $point;
    }
    
    public function setDayInvoice($dayInvoice)
    {
        $this->dayInvoice = $dayInvoice;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function getCatalogs()
    {
        return $this->catalogs;
    }

    public function getOrdersReceived()
    {
        return $this->ordersReceived;
    }

    public function getOrdersGenerated()
    {
        return $this->ordersGenerated;
    }

    public function setRole($role){
        $this->role=$role;
    }
    
    public function getRole(){
        return $this->role;
    }
    
    public function setCredito($credito)
    {
        $this->credito = $credito;
    }
    
    public function setCreditoCongelado($creditoCongelado)
    {
        $this->creditoCongelado = $creditoCongelado;
    }
    
    public function setCreditoNegativo($creditoNegativo)
    {
        $this->creditoNegativo = $creditoNegativo;
    }
    
    
    
    public function setState($state){
        $this->state = $state;
    }
    
    public function setCountry($country){
        $this->country = $country;
    }
    
    public function setCity($city){
        $this->city = $city;
    }

    public function getData()
    {
        $data = array();
        $data['id'] = $this->id;
        $data['firstName'] = $this->firstName;
        $data['lastName'] = $this->lastName;
        $data['username'] = $this->username;
        $data['type'] = $this->type;
        $data['role'] = $this->role->getId();

        return $data;
    }

    public function getPoint()
    {
        return $this->point;
    }

    public function getTypeString($type = false)
    {
        $str = '';
        $current = $this->getType();
        
        if($type != false)
        {
            $current = $type;
        }

        switch($current)
        {
            case self::USER_ADMIN: $str = 'Administrador'; break;
            case self::USER_CLIENT: $str = 'Cliente'; break;
            case self::USER_DRIVER: $str = 'Conductor'; break;
            case self::USER_SECRETARY: $str = 'Secretaria'; break;
            
        }
        return $str;
    }
    
    public function getTerms()
    {
    	return $this->terms;
    }
    
    public function getService()
    {
    	return $this->service;
    }
    
    public function getPrivacy()
    {
    	return $this->privacy;
    }
    
    public function setTerms($terms)
    {
    	$this->terms = $terms;
    }
    
    public function setService($service)
    {
    	$this->service = $service;
    }
    
    public function setPrivacy($privacy)
    {
    	$this->privacy = $privacy;
    }
    
    public function getRfc()
    {
    	return $this->rfc;
    }
    
    public function setRfc($rfc)
    {
    	$this->rfc = $rfc;
    }
    
    public function getStreet()
    {
    	return $this->street;
    }
    
    public function setStreet($street)
    {
    	$this->street = $street;
    }
    
    public function getSuburb()
    {
    	return $this->suburb;
    }
    
    public function setSuburb($suburb)
    {
    	$this->suburb = $suburb;
    }
    
    public function getNumber()
    {
    	return $this->number;
    }
    
    public function setNumber($number)
    {
    	$this->number = $number;
    }
    
    public function getNumint()
    {
    	return $this->numint;
    }
    
    public function setNumint($numint)
    {
    	$this->numint = $numint;
    }
    
    public function getZip()
    {
    	return $this->zip;
    }
    
    public function setZip($zip)
    {
    	$this->zip = $zip;
    }
    
    public function getVisible()
    {
    	return $this->visible;
    }
    
    public function setVisible($visible)
    {
    	$this->visible = $visible;
    }
    
    public function getBank()
    {
    	return $this->bank;
    }
    
    public function setBank($bank)
    {
    	$this->bank = $bank;
    }
    
    public function getAccount()
    {
    	return $this->account;
    }
    
    public function setAccount($account)
    {
    	$this->account = $account;
    }
    
    public function getClabe()
    {
    	return $this->clabe;
    }
    
    public function setClabe($clabe)
    {
    	$this->clabe = $clabe;
    }
    
    public function getMail()
    {
    	return $this->mail;
    }
    
    public function setMail($mail)
    {
    	$this->mail = $mail;
    }
    
    public function getPhone()
    {
    	return $this->phone;
    }
    
    public function setPhone($phone)
    {
    	$this->phone = $phone;
    }
    
    public function getCrdPay()
    {
    	return $this->crdpay;
    }
    
    public function setCrdPay($crdpay)
    {
    	$this->crdpay = $crdpay;
    }
    
    public function getDelyPay()
    {
    	return $this->delypay;
    }
    
    public function setDelyPay($delypay)
    {
    	$this->delypay = $delypay;
    }

    public function getBusinessName()
    {
    	return $this->businessname;
    }
    
    public function setBusinessName($businessname)
    {
    	$this->businessname = $businessname;
    }
    
    public function getToken()
    {
    	return $this->token;
    }
    
    public function setToken($token)
    {
    	$this->token = $token;
    }
    
    public function getMsg(){
    	return $this->msgtoclients;
    }
    
    public function setMsg($msgtoclients){
   		$this->msgtoclients = $msgtoclients;
    }

    public function getMsg2(){
    	return $this->msgtoclients2;
    }
    
    public function setMsg2($msgtoclients2){
   		$this->msgtoclients2 = $msgtoclients2;
    }

}
