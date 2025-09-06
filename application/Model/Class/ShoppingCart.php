<?php

class Class_ShoppingCart
{

    protected $_idSeller;
    protected $_itemcount;
    protected $_items;
    protected $_subtotal;
    protected $_comments;


    // CONSTRUCTOR FUNCTION
    //function cart() {}
    public function __construct()
    {
        $this->_idSeller = 0;
        $this->_subtotal = 0;
        $this->_itemcount = 0;
        $this->_items = array();
    }

    public function setIdSeller($id)
    {
        $this->_idSeller = $id;
    }

    public function setComments($comments)
    {
        $this->_comments = $comments;
    }

    /**
     * Inserta un nuevo producto al carrito de compras     
     * @param DefaultDb_Entities_Product $product
     * @param DefaultDb_Entities_ProductVariants $variant
     * @return bool
     */
    public function add_item($product,$variant=null)
    {
        if($this->_idSeller == 0)
            $this->_idSeller = $product->getClient ()->getId();
        elseif($this->_idSeller != $product->getClient()->getId())
            return false;
            
        // Si el producto ya existe, incrementamos la cantidad
        if (isset($this->_items[$product->getId()]))
        {
            //si tiene variantes contamos cuantas variantes de su id tiene
            if($variant!==null){
                if(isset($this->_items[$product->getId()]['variant'][$variant->getId()])){
                    $this->_items[$product->getId()]['variant'][$variant->getId()]['quantity']++;
                }
                else{
                    $this->_items[$product->getId()]['variant'][$variant->getId()]['quantity']= 1;
                }
            }
            $this->_items[$product->getId()]['quantity']++;
            $this->_update_total();
        }
        // Es nuevo producto en el carrito
        else
        {
            $this->_items[$product->getId()] = array();
            $this->_items[$product->getId()]['price'] = $product->getPrice();
            $this->_items[$product->getId()]['quantity'] = 1;
            $this->_items[$product->getId()]['variant']=array();
            if($variant!==null)
            {
                $this->_items[$product->getId()]['variant'][$variant->getId()]['quantity']= 1;
            }
        }
        $this->_update_total();
        return true;
    }

    /**
     * Actualiza la cantidad de un producto y el total
     * @param DefaultDb_Entities_Product $product
     * @param int $item_qty
     * @param DefaultDb_Entities_ProductVariants $variant
     * @return bool
     */
    function update_item($product, $item_qty, $variant = null)
    {
        //Validamos el
        if (preg_match("/^[0-9-]+$/i", $item_qty))
        {
            if ($item_qty < 1)
            {
                $this->del_item($product,$variant);
            }
            else
            {
                if($variant !==null){
                    $newQuantity = $item_qty - $this->_items[$product->getId()]['variant'][$variant->getId()]['quantity'];
                    $this->_items[$product->getId()]['variant'][$variant->getId()]['quantity'] = $item_qty;
                    $this->_items[$product->getId()]['quantity'] = $this->_items[$product->getId()]['quantity'] + $newQuantity;
                }
                else
                    $this->_items[$product->getId()]['quantity'] = $item_qty;
            }
            $this->_update_total();
            return true;
        }
        return false;
    }

    /**
     * Remueve un articulo del carrito
     * @param DefaultDb_Entities_Product $product
     * @param DefaultDb_Entities_ProductVariants $variant
     */
    public function del_item($product,$variant=null)
    {
        $ti = array();
        //Reseteamos la cantidad del producto en el carrito
        if($variant!==null) {
            //si es una variante eliminamos solo la variante y actualizamos la cantidades del carrito
            $qtyVariantOld = $this->_items[$product->getId()]['variant'][$variant->getId()]['quantity'];
            $this->_items[$product->getId()]['variant'][$variant->getId()]['quantity'] = 0;
            $this->_items[$product->getId()]['quantity'] = $this->_items[$product->getId()]['quantity'] - $qtyVariantOld;
            $tiv=array();
            foreach ($this->_items[$product->getId()]['variant'] as $key =>$variantArray)
            {
                if ($key != $variant->getId())
                {
                    $tiv[$key] = $variantArray;
                }
            }
            $this->_items[$product->getId()]['variant']=$tiv;
        }
        else {
            $this->_items[$product->getId()]['quantity'] = 0;
        }
        //verificamos si el producto quedo con cero cantidades entonces eliminamos el item del carrito
        if($this->_items[$product->getId()]['quantity']==0)
        {
            //Eliminamos el producto buscado del carrito
            foreach ($this->_items as $keyItem => $item)
            {
                if ($keyItem != $product->getId())
                {
                    $ti[$keyItem] = $item;
                }
            }
            $this->_items = $ti;
        }
        if(count($this->_items)<=0)
            $this->empty_cart ();
        $this->_update_total();
        return true;
    }

    /**
     * Vacia el carrito de compras
     */
    public function empty_cart()
    {
        $this->_idSite = 0;
        $this->_idUser = 0;
        $this->_orderId = 0;
        $this->_Payment_Amount = 0;
        $this->_itemcount = 0;
        $this->_items = array();
        $this->_itemprices = array();
        $this->_itemqtys = array();
        $this->_itemname = array();
        $this->_subtotal = 0;
        $this->_shippingCost = 0;
        $this->_idShipping = 0;
        $this->_idSeller = 0;
    }

    // Funcion para recalcular el total
    protected function _update_total()
    {
        $this->_itemcount = 0;
        $this->_subtotal = 0;
        foreach ($this->_items as $item)
        {
            $this->_subtotal = $this->_subtotal + ($item['price'] * $item['quantity']);
            $this->_itemcount += $item['quantity'];
        }
    }

    public function getItemCount()
    {
        return $this->_itemcount;
    }

    public function getItems()
    {
        return $this->_items;
    }

    public function mergeProductData()
    {
        $mergedData = array();
        foreach ($this->_items as $itemId)
        {
            $mergedData[] = array('id' => $itemId, 'name' => $this->_itemname[$itemId], 'qty' => $this->_itemqtys[$itemId], 'price' => $this->_itemprices[$itemId], 'variantId' => $this->_variants[$itemId]);
        }
        return $mergedData;
    }

    public function getSubtotal()
    {
        return $this->_subtotal;
    }

    public function getIdSeller()
    {
        return $this->_idSeller;
    }
    
    /**
     * Obtenemos el precio del item
     * @param type $product
     * @return type
     */
    public function getPriceItem($product)
    {
        return isset($this->_items[$product->getId()]['price']) ? $this->_items[$product->getId()]['price']:0;
    }
    
    /**
     * Obtenemos la cantidad del item del carrito
     * @param type $product
     * @param type $variant
     * @return type
     */
    public function getQtyItem($product, $variant = null)
    {
        if($variant === null)
            return isset($this->_items[$product->getId()]['quantity'])?$this->_items[$product->getId()]['quantity']:0;
        else
            return isset($this->_items[$product->getId()]['variant'][$variant->getId()]['quantity']) ? $this->_items[$product->getId()]['variant'][$variant->getId()]['quantity'] : 0;
    }
    
    public function getComments()
    {
        return $this->_comments;
    }

}
