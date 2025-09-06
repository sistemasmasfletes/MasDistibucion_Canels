<?php

class BackStore_creteShippingController extends JController 
{
    public function categoriasAction() {

        $em = $this->getEntityManager('DefaultDb');
        $query = $em->getRepository('DefaultDb_Entities_Category')->createQueryBuilder('m')->select('m')->getQuery()->getResult();
        $x = 0;
        foreach ($query as $q) {
            $result[] = array('id' => $q->getId(), 'nombre' => $q->getName());
            $datos = $result[$x];
            $x++;
        }echo '{"categorias": ' . json_encode($result) . '}';
    }  
}

