<?php
/**
 * @Entity(repositoryClass="DefaultDb_Repositories_TransactionsRepository")
 * @Table(name="transactions", indexes={@index(name="transaction_id_IDX", columns={"transaction_id"})})
 */

class DefaultDb_Entities_Transactions
{
    /**
     * @Id @GeneratedValue @Column(type="integer", name="id")
     * @var integer
     */
    protected $id;
    
    /**
    * @Column(type="integer", name="transaction_id", nullable=true)
    * @var integer
    */
    protected $transactionId;
    
    /**
    * @ManyToOne(targetEntity="DefaultDb_Entities_TransactionType")
    */
    protected $transactionType;

    /**    
     * @Column(type="integer", name="status", nullable=true)
     * @var integer
     * Estatus de la actividad 
     * 0 En espera(de generar pedido)
     * 1 Por recolectar
     * 2 En ruta
     * 3 Entregada
     * 4 En centro de intercambio
     * 5 Detenida
     * 6 Rechazado
    */
    protected $status;    
    
    /**
    * @ManyToOne(targetEntity="DefaultDb_Entities_RoutePointActivity")
    *@JoinColumn(nullable=true)
    */
    protected $statusPoint;

   
    public function getId(){
        return $this->id;
    }
   
    public function getTransactionId(){
        return $this->transactionId;
    } 
    
    public function getStatus(){
       return $this->status;
    }

    public function getTransactionType(){
        return $this->$transactionType;
    }

    public function getStatusPoint(){
        return $this->statusPoint;
    }

    public function setId($id){
        $this->id = $Id;
    }
   
    public function setTransactionId($transactionId){
        $this->transactionId = $transactionId;
    } 

    public function setStatus($status){
        $this->status = $status;
    }

    public function setTransactionType($transactionType){
        $this->transactionType = $transactionType;
    }

    public function setStatusPoint($statusPoint){
        $this->statusPoint = $statusPoint;
    }
}