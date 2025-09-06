<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_PromotionScheduleRepository")
 * @Table(name="promotion_schedule")
 */


class DefaultDb_Entities_PromotionSchedule
{
    const STATUS_POR_ENTREGAR = 1;
    const STATUS_ENTREGADO = 3;
    const STATUS_RECHAZADO = 6;
    const STATUS_NO_ENTREGADO = 7;

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;

     /**     
     * @ManyToOne(targetEntity="DefaultDb_Entities_Promotion")
     */
    protected $promotion;

    /**
     * @Column(type="datetime", name="creation_date")
     * @var DateTime
     */
    protected $creationDate;

    /**
     * @Column(type="datetime", name="promotion_date")
     * @var DateTime
     */
    protected $promotionDate;

    /**     
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     */
    protected $user;

    /**     
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     */
    protected $client;

    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_Point")
     */
    protected $point;

    /**     
     * @ManyToOne(targetEntity="DefaultDb_Entities_RoutePointActivity")
     * @JoinColumn(name="activitypoint_id")
     */
    protected $activityPoint;

    /**     
     * @ManyToOne(targetEntity="DefaultDb_Entities_PackageToOrder")
     * @JoinColumn(name="packageorder_id")
     */
    protected $packageOrder;

    /**
     * @Column(type="integer", name="payment_status")
     * @var int
     */
    protected $paymentStatus=0;

    /**
     * @Column(type="integer", name="shipping_status")
     * @var int 
     */
    protected $shippingStatus = 0;

    /**
     * @Column(type="integer", name="consumer_type", nullable=true)
     * @var int 
     * 1. No es consumidor
     * 2. Podría ser consumidor
     * 3. Es consumidor
     * 4. Es un gran consumidor
     */
    protected $consumerType;

     /**
     * @Column(type="integer", name="interest_level", nullable=true)
     * @var int
     * 1. No interesado
     * 2. Medianamente interesado
     * 3. Interesado
     * 4. Muy interesado
     */
    protected $interestLevel;

    /**
     * @Column(type="integer", name="request", nullable=true)
     * @var int
     * 1. N/A
     * 2. Solicita llamada
     * 3. Solicita cita
     * 4. Solicita producto
    */
    protected $request;

    /**
     * @Column(type="string", length=50, name="telephone", nullable=true)
     */    
    protected $telephone;


    /**
     * @Column(type="string", length=60, name="receiving_user", nullable=true)
     * @var string
     */
    protected $receivingUser;

    /**
     * @Column(type="string", length=254, name="comments", nullable=true)
     * @var string
     */
    protected $comments;

    public function setId($id){
        $this->id=$id;
    }

    public function setPromotion($promotion){
        $this->promotion = $promotion;
    }

    public function setCreationDate($creationDate){
        $this->creationDate = $creationDate;
    }
    
    public function setPromotionDate($promotionDate){
        $this->promotionDate = $promotionDate;
    }

    public function setUser($user){
        $this->user = $user;
    }

    public function setClient($client){
        $this->client = $client;
    }

    public function setPoint($point){
        $this->point = $point;
    }

    public function setActivityPoint($activityPoint){
        $this->activityPoint = $activityPoint;
    }

    public function setPackageOrder($packageOrder){
        $this->packageOrder = $packageOrder;
    }

    public function setPaymentStatus($paymentStatus){
        $this->paymentStatus = $paymentStatus;
    }

    public function setShippingStatus($shippingStatus){
        $this->shippingStatus = $shippingStatus;
    }

    public function setConsumerType($consumerType){
        $this->consumerType = $consumerType;
    }

    public function setInterestLevel($interestLevel){
        $this->interestLevel = $interestLevel;
    }

    public function setRequest($request){
        $this->request = $request;
    }

    public function setTelephone($telephone){
        $this->telephone = $telephone;
    }

    public function setReceivingUser($receivingUser){
        $this->receivingUser = $receivingUser;
    }

    public function setComments($comments){
        $this->comments = $comments;
    }

    public function getId(){
        return $this->id;
    }

    public function getPromotion(){
        return $this->promotion;
    }

    public function getCreationDate(){
        return $this->creationDate;
    }
    
    public function getPromotionDate(){
        return $this->promotionDate;
    }

    public function getUser(){
        return $this->user;
    }

    public function getClient(){
        return $this->client;
    }

    public function getPoint(){
        return $this->point;
    }

    public function getActivityPoint(){
        return $this->activityPoint;
    }

    public function getPackageOrder(){
        return $this->packageOrder;
    }

    public function getPaymentStatus(){
        return $this->paymentStatus;
    }

    public function getShippingStatus(){
        return $this->shippingStatus;
    }

    public function getConsumerType(){
        return $this->consumerType;
    }

    public function getInterestLevel(){
        return $this->interestLevel;
    }

    public function getRequest(){
        return $this->request;
    }

    public function getTelephone(){
        return $this->telephone;
    }

    public function getReceivingUser(){
        return $this->receivingUser;
    }

    public function getComments(){
        return $this->comments;
    }
}