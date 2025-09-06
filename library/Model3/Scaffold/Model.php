<?php

class Model3_Scaffold_Model
{

    protected $_class;
    protected $_repository;
    protected $_columns;
    protected $_columnTypes = array(
        'integer' => 'text',
        'password' => 'password',
        'decimal' => 'text',
        'float' => 'text',
        'string' => 'text',
        'varchar' => 'text',
        'boolean' => 'checkbox',
        'timestamp' => 'text',
        'time' => 'text',
        'date' => 'text',
        'datetime' => 'text',
        'enum' => 'select',
        'textarea' => 'textarea'
    );
    protected $_defaultValidators = array(
        'integer' => 'int',
        'float' => 'float',
        'double' => 'float'
    );
    protected $_validators = array();
    protected $_ignoreColumns = array();
    protected $_fieldInputs = array();
    protected $_fieldLabels = array();
    protected $_fieldOrder = array();
    protected $_enums = array();
    protected $_currentId = null;
    protected $_view = null;
    protected $_editLink = true;
    protected $_deleteLink = true;
    protected $_editController = null;
    protected $_editAction = 'edit';
    protected $_deleteController = null;
    protected $_deleteAction = 'delete';
    protected $_filterField = null;
    protected $_filterValue = null;
    protected $_externalActions = array();
    private $_cache = array();
    protected $_formElementPrepend = '';
    protected $_formElementAppend = '';
    protected $_formElementInputClass = '';

    public function __construct()
    {
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        /* @var $em Doctrine\ORM\EntityManager */
        $this->_repository = $em->getRepository($this->_class);
    }

    public function setCurrentId($id)
    {
        $this->_currentId = $id;
    }

    public function setView($view)
    {
        $this->_view = $view;
    }

    public function setEditController($editController)
    {
        $this->_editController = $editController;
    }

    public function setEditAction($editAction)
    {
        $this->_editAction = $editAction;
    }

    public function setDeleteController($deleteController)
    {
        $this->_deleteController = $deleteController;
    }

    public function setDeleteAction($deleteAction)
    {
        $this->_deleteAction = $deleteAction;
    }

    public function addEnum($field, $options)
    {
        $this->_enums[$field] = $options;
    }

    public function addIgnoreColumn($field)
    {
        if (!in_array($field, $this->_ignoreColumns))
            $this->_ignoreColumns[] = $field;
    }

    public function deleteIgnoreColumn($field)
    {
        $key = array_search($field, $this->_ignoreColumns);
        if ($key !== null)
        {
            unset($this->_ignoreColumns[$key]);
        }
    }

    public function getFilterField()
    {
        return $this->_filterField;
    }

    public function getFilterValue()
    {
        return $this->_filterValue;
    }

    public function setFilterField($filterField)
    {
        $this->_filterField = $filterField;
    }

    public function setFilterValue($filterValue)
    {
        $this->_filterValue = $filterValue;
    }

    public function setFormElementPrepend($formElementPrepend)
    {
        $this->_formElementPrepend = $formElementPrepend;
    }

    public function setFormElementAppend($formElementAppend)
    {
        $this->_formElementAppend = $formElementAppend;
    }

    public function setFormElementInputClass($formElementInputClass)
    {
        $this->_formElementInputClass = $formElementInputClass;
    }

    public function generateForm($formOptions = null)
    {
        if (!is_array($formOptions))
            $formOptions = array();
        if (!key_exists('method', $formOptions))
            $formOptions['method'] = 'post';
        $strOptions = '';
        foreach ($formOptions as $option => $value)
        {
            $strOptions .= $option . ' = "' . $value . '"';
        }

        $current = false;
        if ($this->_currentId != null)
            $current = $this->_repository->find($this->_currentId);

        echo '<form ' . $strOptions . '>';
        foreach ($this->_fieldOrder as $name)
        {
            if (isset($this->_columns[$name]['formIgnore']) && $this->_columns[$name]['formIgnore'] == true)
                continue;
            $this->generateFormElement($current, $name, $this->_columns[$name]);
        }
        foreach ($this->_columns as $name => $def)
        {
            if (isset($this->_columns[$name]['formIgnore']) && $this->_columns[$name]['formIgnore'] == true)
                continue;
            if (!in_array($name, $this->_fieldOrder))
                $this->generateFormElement($current, $name, $def);
        }
        echo $this->_formElementPrepend;
        echo '<input type="submit" class="btn-default sin-padding pers-btn icono-enviar tam-normal" style="color: transparent;" />';
        echo $this->_formElementAppend;
        echo '</form>';
    }

    public function generateFormElement($record, $fieldName, $fieldDefinition = null)
    {
        $aux = 'get' . ucfirst($fieldName);

        echo $this->_formElementPrepend;
        echo '<label id="label_' . $fieldName . '" for="' . $fieldName . '" >';
        if (isset($fieldDefinition['label']))
            echo $fieldDefinition['label'];
        else
            echo $fieldName;
        echo ': </label>';
        
        if (isset($fieldDefinition['relation']))
        {
            $dbs = Model3_Registry::getInstance()->get('databases');
            $em = $dbs['DefaultDb'];
            /* @var $em Doctrine\ORM\EntityManager */

            $entity = $em->getRepository($fieldDefinition['relation']['entity']);
            $foreignRecords = $entity->findAll();

            $auxKey = 'get' . $fieldDefinition['relation']['key'];
            $auxDisplay = 'get' . $fieldDefinition['relation']['display'];

            echo '<select name="' . $fieldName . '" id="' . $fieldName . '" class = "' . $this->_formElementInputClass . '">';
            echo '<option value="0">Seleccione...</option>';
            foreach ($foreignRecords as $foreignRecord)
            {
                $selected = '';                
                if ($record != false && $record != null && $record->$aux() != null && $foreignRecord->$auxKey() == $record->$aux()->$auxKey())
                    $selected = ' selected = "selected" ';
                echo '<option value="' . $foreignRecord->$auxKey() . '" ' . $selected . '>' . $foreignRecord->$auxDisplay() . '</option>';
            }
            echo '</select>';
        } else
        {
            if (isset($fieldDefinition['enum']))
            {
                echo '<select name="' . $fieldName . '" id="' . $fieldName . '" class = "' . $this->_formElementInputClass . '" >';
                echo '<option value="0">Seleccione...</option>';

                foreach ($fieldDefinition['enum'] as $enumValue => $enumDisplay)
                {
                    $selected = '';
                    if ($record != false && $enumValue == $record->$aux())
                        $selected = ' selected = "selected" ';
                    echo '<option value="' . $enumValue . '" ' . $selected . '>' . $enumDisplay . '</option>';
                }
                echo '</select>';
            }
            else
            {
                $typeInput = 'text';
                if (key_exists($fieldName, $this->_fieldInputs))
                    $typeInput = $this->_fieldInputs[$fieldName];
                elseif ($fieldDefinition != null)
                {
                    if (isset($fieldDefinition['type']))
                        $typeInput = $this->_columnTypes[$fieldDefinition['type']];
                }
                if ($typeInput == 'textarea')
                {
                    echo '<textarea name="' . $fieldName . '" id="' . $fieldName . '" class = "' . $this->_formElementInputClass . '" >';
                    if ($record != false)
                        echo $record->$fieldName;
                    echo '</textarea>';
                }
                else
                {
                    echo '<input type="' . $typeInput . '" name="' . $fieldName . '" id="' . $fieldName . '"';
                    if ($record != false)
                    {
                        if (isset($fieldDefinition['type']) && $fieldDefinition['type'] == 'datetime')
                            echo ' value="' . $record->$aux()->format($fieldDefinition['datetime']['format']);
                        else
                            echo ' value="' . $record->$aux();
                    }
                    echo '" class = "' . $this->_formElementInputClass . '" />';
                }
            }
        }
        echo '<br/>';
        echo $this->_formElementAppend;
    }

    public function saveForm($data)
    {
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        /* @var $em Doctrine\ORM\EntityManager */

        if ($this->_currentId != null)
            $doctrineModel = $this->_repository->find($this->_currentId);
        else
            $doctrineModel = new $this->_class;
        foreach ($this->_columns as $name => $def)
        {
            if (isset($def['id']) && $def['id'] == true)
                continue;
            if (isset($data[$name]))
            {
                $value = $data[$name];
                if (isset($def['relation']))
                {
                    if(!($data[$name] instanceof $def['relation']['entity']))
                    {
                        $entity = $em->getRepository($def['relation']['entity']);
                        $value = $entity->find($data[$name]);
                    }
                    else
                    {
                        $value = $data[$name];
                    }
                }

                $aux = 'set' . ucfirst($name);
                $doctrineModel->$aux($value);
            }
        }

        if ($this->_filterField !== null && $this->_filterValue !== null)
        {
            $filterField = $this->_filterField;
            $doctrineModel->$filterField = $this->_filterValue;
        }
        $em->persist($doctrineModel);
        $em->flush();
    }

    public function deleteRecord($id)
    {
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        /* @var $em Doctrine\ORM\EntityManager */

        $doctrineModel = $this->_repository->find($id);
        $em->remove($doctrineModel);
        $em->flush();
    }

    public function generateTable($records = null)
    {
        if ($records == null)
        {
            if ($this->_filterField == null || $this->_filterValue == null)
                $records = $this->_repository->findAll();
            else
                $records = $this->_repository->findBy($this->_filterField, $this->_filterValue);
        }

        echo '<table class="table table-striped table-bordered table-condensed" >';
        echo '<thead>';
        echo '<tr>';

        foreach ($this->_fieldOrder as $name)
        {
            if (isset($this->_columns[$name]['tableIgnore']) && $this->_columns[$name]['tableIgnore'] == true)
                continue;
            echo '<th>';
            if (isset($this->_columns[$name]['label']))
                echo $this->_columns[$name]['label'];
            else
                echo $name;
            echo '</th>';
        }

        foreach ($this->_columns as $name => $def)
        {
            if (isset($def['tableIgnore']) && $def['tableIgnore'] == true)
                continue;
            if (!in_array($name, $this->_fieldOrder))
            {
                echo '<th>';
                if (isset($def['label']))
                    echo $def['label'];
                else
                    echo $name;
                echo '</th>';
            }
        }

        if ($this->_editLink)
        {
            echo '<th>';
            echo 'Edit';
            echo '</th>';
        }
        if ($this->_deleteLink)
        {
            echo '<th>';
            echo 'Delete';
            echo '</th>';
        }
        foreach ($this->_externalActions as $nameAction => $externalAction)
        {
            echo '<th>';
            echo $nameAction;
            echo '</th>';
        }
        echo '</tr>';
        echo '</thead>';
        echo '<tbody id="myTable">';
        foreach ($records as $record)
        {
            $editParams = array();
            if ($this->_editController != null)
            {
                $editParams['controller'] = $this->_editController;
            }
            if ($this->_editAction != null)
            {
                $editParams['action'] = $this->_editAction;
            }
            $editParams['id'] = $record->getId();
            if ($this->_filterField != null && $this->_filterValue != null)
            {
                $editParams['filterValue'] = $this->_filterValue;
            }

            // Se preparan los parametros para la accion delete
            $deleteParams = array();
            if ($this->_deleteController != null)
            {
                $deleteParams['controller'] = $this->_deleteController;
            }
            if ($this->_deleteAction != null)
            {
                $deleteParams['action'] = $this->_deleteAction;
            }
            $deleteParams['id'] = $record->getId();
            if ($this->_filterField != null && $this->_filterValue != null)
            {
                $deleteParams['filterValue'] = $this->_filterValue;
            }

            echo '<tr>';
            foreach ($this->_fieldOrder as $name)
            {
                if (in_array($name, $this->_ignoreColumns))
                    continue;
                echo '<td>';
                $this->generateTableCell($record, $name, $this->_columns[$name]);
                echo '</td>';
            }

            foreach ($this->_columns as $name => $def)
            {
                if (isset($def['tableIgnore']) && $def['tableIgnore'] == true)
                    continue;
                if (!in_array($name, $this->_fieldOrder))
                {
                    echo '<td>';
                    $this->generateTableCell($record, $name, $def);
                    echo '</td>';
                }
            }
            if ($this->_editLink)
            {
                echo '<td>';
                echo '<a class="edit-link" href="' . $this->_view->url($editParams) . '">Editar</a>';
                echo '</td>';
            }
            if ($this->_deleteLink)
            {
                echo '<td>';
                echo '<a class="delete-link" href="' . $this->_view->url($deleteParams) . '">Eliminar</a>';
                echo '</td>';
            }
            foreach ($this->_externalActions as $nameAction => $externalAction)
            {
                $params = $externalAction;
                $params['id'] = $record->getId();
                if ($this->_filterField != null && $this->_filterValue != null)
                {
                    $params['filterValue'] = $this->_filterValue;
                }
                echo '<td>';
                echo '<a href="' . $this->_view->url($params) . '">' . $nameAction . '</a>';
                echo '</td>';
            }
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    }

    protected function generateTableCell($record, $fieldName, $fieldDefinition)
    {
        $aux = 'get' . ucfirst($fieldName);
        if (isset($fieldDefinition['relation']))
        {
            $entity = $record->$aux();
            if ($entity)
            {
                $aux2 = 'get' . ucfirst($fieldDefinition['relation']['display']);
                echo $entity->$aux2();
            }
        }
        else
        {
            if (isset($fieldDefinition['enum']))
            {
                if (key_exists($record->$aux(), $fieldDefinition['enum']))
                    echo $fieldDefinition['enum'][$record->$aux()];
            }
            else
            {
                if (isset($fieldDefinition['type']) && ($fieldDefinition['type'] == 'date' || $fieldDefinition['type'] == 'datetime' || $fieldDefinition['type'] == 'time'))
                {
                    echo $record->$aux()->format($fieldDefinition['datetime']['format']);
                }
                else
                {
                    echo $record->$aux();
                }
            }
        }
    }

    public function generateCSV($records = null, $separator = ",")
    {
        if ($records == null)
        {
            if ($this->_filterField == null || $this->_filterValue == null)
                $records = $this->_repository->findAll();
            else
                $records = $this->_repository->findBy($this->_filterField, $this->_filterValue);
        }
        $identifier = $this->_repository->getIdentifier();

        foreach ($this->_fieldOrder as $name)
        {
            if (in_array($name, $this->_ignoreColumns))
                continue;
            if (key_exists($name, $this->_fieldLabels))
                echo $this->_fieldLabels[$name];
            else
                echo $name;
            echo $separator;
        }

        foreach ($this->_columns as $name => $def)
        {
            if (in_array($name, $this->_ignoreColumns))
                continue;
            if (!in_array($name, $this->_fieldOrder))
            {
                if (key_exists($name, $this->_fieldLabels))
                    echo $this->_fieldLabels[$name];
                else
                    echo $name;
                echo $separator;
            }
        }
        echo "\n";
        foreach ($records as $record)
        {
            foreach ($this->_fieldOrder as $name)
            {
                if (in_array($name, $this->_ignoreColumns))
                    continue;
                $this->generateCSVValue($record, $name, $this->_columns[$name]);
                echo $separator;
            }

            foreach ($this->_columns as $name => $def)
            {
                if (in_array($name, $this->_ignoreColumns))
                    continue;
                if (!in_array($name, $this->_fieldOrder))
                {
                    $this->generateCSVValue($record, $name, $def);
                    echo $separator;
                }
            }
            echo "\n";
        }
    }

    protected function generateCSVValue($record, $fieldName, $fieldDefinition)
    {
        if (key_exists($fieldName, $this->_relations))
        {
            $foreignTable = Doctrine::getTable($this->_relations[$fieldName]['model']);
            $foreignKey = $this->_relations[$fieldName]['key'];
            $foreignDisplay = $this->_relations[$fieldName]['display'];

            if (!key_exists($fieldName, $this->_cache) || !key_exists($record->$fieldName, $this->_cache[$fieldName]))
            {
                $foreignRecord = $foreignTable->find($record->$fieldName);
                if ($foreignRecord)
                {
                    $this->_cache[$fieldName][$record->$fieldName] = $foreignRecord->$foreignDisplay;
                    echo $foreignRecord->$foreignDisplay;
                }
                else
                    $this->_cache[$fieldName][$record->$fieldName] = '';
            }
            else
            {
                echo $this->_cache[$fieldName][$record->$fieldName];
            }
        }
        else
        {
            if (key_exists($fieldName, $this->_enums))
            {
                if (key_exists($record->$fieldName, $this->_enums[$fieldName]))
                    echo $this->_enums[$fieldName][$record->$fieldName];
            }
            else
            {
                echo $record->$fieldName;
            }
        }
    }

}