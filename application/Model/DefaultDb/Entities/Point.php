<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_PointRepository")
 * @Table(name="points",uniqueConstraints={@UniqueConstraint(name="code_unique", columns={"name", "code"})})
 */


class DefaultDb_Entities_Point
{
    //type constants
    const TYPE_SALE_POINT = 1;
    const TYPE_EXCHANGE_CENTER = 2;
    
    //status constants
    const STATUS_NORMAL = 1;
    const STATUS_PAUSED = 2;
    const STATUS_CANCELED = 3;
    
    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Address")
     * @JoinColumn(nullable=true)
     */
    protected $address;
    
    /**
     * @Column(type="integer", name="extNumber", nullable=true)
     * @var integer
     */
    protected $extNumber;
    
    /**
     * @Column(type="string", name="intNumber", nullable=true)
     * @var string
     */
    protected $intNumber;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     * @JoinColumn(name="controller_id", referencedColumnName="id", nullable=true)
     */
    protected $controller;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Category")
     * @JoinColumn(nullable=true)
     */
    protected $categoryId;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Classification")
     * @JoinColumn(nullable=true)
     */
    protected $classificationId;
    
    /**
     * @Column(type="string", name="code", nullable=true)
     * @var string
     */
    protected $code;
    
    /**
     * @Column(type="string", name="name", nullable=true)
     * @var string
     */
    protected $name;
    
    /**
     * @Column(type="integer", name="type", nullable=true)
     * @var integer
     */
    protected $type;
    
    /**
     * @Column(type="integer", name="status", nullable=true)
     * @var integer
     */
    protected $status;
    
    /**
     * @Column(type="time", name="opening_time", nullable=true)
     * @var time
     */
    protected $openingTime;
    
    /**
     * @Column(type="time", name="closing_time", nullable=true)
     * @var time
     */
    protected $closingTime;

    /**
     * @Column(type="string", name="webpage", length=255, nullable=true)
     * @var string
     */
    protected $webpage;
    
    /**
     * @Column(type="string", name="comments", nullable=true)
     * @var string
     */
    protected $comments;
    
    /**
     * @Column(type="boolean", name="deleted", nullable=true)
     * @var boolean
     */
    protected $deleted;
    
    /**
     * @Column(type="string", name="urlGoogleMaps", length=350, nullable=true)
     * @var string
     */
    protected $urlGoogleMaps;
    
    /**
     * @Column(type="string", name="neighborhood", length=255, nullable=true)
     * @var string
     */
    protected $neighborhood;
    
    /**
     * @Column(type="integer", name="zipcode", nullable=true)
     * @var integer
     */
    protected $zipcode;
    
    /**
     * @Column(type="integer", name="city_id", nullable=true)
     * @var integer
     */
    protected $city;
    
    /**
     * @Column(type="integer", name="state_id", nullable=true)
     * @var integer
     */
    protected $state;
    
    /**
     * @Column(type="integer", name="country_id", nullable=true)
     * @var integer
     */
	protected $country;
    
    /**
     * @Column(type="time", name="activitytime", nullable=true)
     * @var time
     */
    protected $acTime;

    /**
     * @Column(type="string", nullable=true)
     * @var string
     */
    protected $phone;
    
    /**
     * @Column(type="string", nullable=true)
     * @var string
     */
    protected $contact;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getCountry(){
    	return $this->country;
    }
    
    public function getState(){
    	return $this->state;
    }
    
    public function getCity(){
    	return $this->city;
    }
    
    public function getCode()
    {
        return $this->code;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getAddress(){
        return $this->address;
    }
    
    public function getExtNumber(){
        return $this->extNumber;
    }
    
    public function getIntNumber(){
        return $this->intNumber;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function getStatus()
    {
        return $this->status;
    }
    
    public function getStatusString()
    {
        $cad = "";
        switch($this->status)
        {
            case self::STATUS_NORMAL:
                $cad = 'Activa';
                break;
            case self::STATUS_PAUSED:
                $cad = 'Pausada';
                break;
            case self::STATUS_CANCELED:
                $cad = 'Cancelada';
                break;
        }
        return $cad;
    }
    
    public function getController()
    {
        return $this->controller;
    }
    
    public function getOpeningTime(){
        return $this->openingTime;
    }
    
    public function getClosingTime(){
        return $this->closingTime;
    }
    
    public function getComments(){
        return $this->comments;
    }
    
    public function getDeleted(){
        return $this->deleted;
    }
    
    public function getWebpage(){
        return $this->webpage;
    }
    
    public function getClassificationId(){
        return $this->classificationId;
    }
    
    public function getCategoryId(){
        return $this->categoryId;
    }
    
    public function getUrlGoogleMaps(){
        return $this->urlGoogleMaps;
    }

    public function getFullAddress(){
        $fullAddress='';
        if($this->address){
            if($this->address->getAddress())
                $fullAddress=$this->address->getAddress();
            if($this->extNumber)
                $fullAddress.= ' ' . $this->extNumber;
            if($this->intNumber)
                $fullAddress.= ' Int '. $this->intNumber;
            if($this->address->getNeighborhood ())
                $fullAddress.= ' Col. '. $this->address->getNeighborhood();
        }
        
        return $fullAddress;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setCountry($country){
    	$this->country = $country;
    }
    
    public function setState($state){
    	$this->state = $state;
    }
    
    public function setCity($city){
    	$this->city = $city;
    }
    
    public function setCode($code)
    {
        $this->code = $code;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function setAddress($address){
        $this->address = $address;
    }
    
    public function setExtNumber($extNumber){
        $this->extNumber = $extNumber;
    }
    
    public function setIntNumber($extNumber){
        $this->intNumber = $extNumber;
    }

    public function setType($type)
    {
        $this->type = $type;
    }
    
    public function setStatus($status)
    {
        $this->status = $status;
    }
    
    public function setController($controller)
    {
        $this->controller = $controller;
    }
    
    public function setOpeningTime($openingTime){
        $this->openingTime = $openingTime;
    }
    
    public function setClosingTime($closingTime){
        $this->closingTime = $closingTime;
    }
    
    public function setComments($comments){
        $this->comments = $comments;
    }
    
    public function setDeleted($deleted){
        $this->deleted = $deleted;
    }
    
    public function setWebpage($webpage){
        $this->webpage = $webpage;
    }
    
    public function setClassificationId($classificationId){
        $this->classificationId = $classificationId;
    }
    
    public function setCategotyId($categoryId){
        $this->categoryId = $categoryId;
    }   
    
    public function setUrlGoogleMaps($urlGoogleMaps){
        $this->urlGoogleMaps = $urlGoogleMaps;
    }    
    
    public function getNeighborhood(){
    	return $this->neighborhood;
    }
    
    public function getZipcode(){
    	return $this->zipcode;
    }
    
    public function setNeighborhood($neighborhood){
    	$this->neighborhood = $neighborhood;
    }
    
    public function setZipcode($zipcode){
    	$this->zipcode = $zipcode;
    }
    
    public function getAcTime(){
    	return $this->acTime;
    }
    
    public function setAcTime($actime){
    	$this->acTime = $actime;
    }
    
    public function getPhone(){
    	return $this->phone;
    }
    
    public function setPhone($phone){
    	$this->phone = $phone;
    }

    public function getContact(){
    	return $this->contact;
    }
    
    public function setContact($contact){
    	$this->contact = $contact;
    }
}
