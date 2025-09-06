<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_InvoicesRepository")
 * @Table(name="invoices")
 */

class DefaultDb_Entities_Invoices {

    /* constantes para tipo de estado de la factura */
    const STATUS_PAID = 1; //Pagado 
    const STATUS_TOPAY = 0; // Por pagar 
    
    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;
   
   /**
     * @Column(type="datetime", name="cutDate")
     * @var DateTime
     */
    protected $cutDate;
   
    /**
     * @ManyToOne(targetEntity="DefaultDb_Entities_User")
     */
    protected $client;
    
    /**
     * @Column(type="integer", name="status")
     * @var integer
     */
    protected $status;
    
    /**
     * @Column(type="integer", name="numOrders")
     * @var integer
     */
    protected $numOrders;

    /**
     * @Column(type="integer", name="priceTotal")
     * @var interger 
     */
    protected $priceTotal;
    
    /**
     * @Column(type="datetime", name="generatedInvoice")
     * @var DateTime
     */
    protected $generatedInvoice;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getCutDate()
    {
        return $this->cutDate;
    }
    
    public function getClient()
    {
        return $this->client;
    }
    
    public function getStatus()
    {
        return $this->status;
    }
    
    public function getNumOrders()
    {
        return $this->numOrders;
    }  

    public function getPriceTotal()
    {
        return $this->priceTotal;
    }
    
    public function getGeneratedInvoice()
    {
        return $this->generatedInvoice;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setCutDate($courtDate)
    {
        $this->cutDate = $courtDate;    
    }
    
    public function setClient($client)
    {
        $this->client = $client;
    }
    
    public function setStatus($status)
    {
        $this->status = $status;
    }
    
    public function setNumOrders($numOrders)
    {
        $this->numOrders = $numOrders;
    }
    
    public function setPriceTotal($priceTotal)
    {
        $this->priceTotal = $priceTotal;
    }
    
    public function setGeneratedInvoice($generatedInvoice)
    {
        $this->generatedInvoice = $generatedInvoice;
    }
}
