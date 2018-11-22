<?php

abstract class Entity
{

    /**
     * Name of the implemented entity and identifier of the database table.
     *
     * @var string
     */
    private $entityType;

    /**
     * Two dimensional array containing the attribute values.
     * <code>$attributes[attributeName] = value;</code>
     *
     * @var array
     */
    private $attributes;

    /**
     * One dimensional array containing the attribute set.
     *
     * @var array
     */
    private $attributeNames;

    protected $model;

    public final function __construct(PlaningModel $model)
    {
        $this->model = $model;
        $this->entityType = $this->initializeEntityType();
        $this->attributeNames = $this->initializeAttributes();
        $this->attributes = array(
            $this->attributeNames
        );
    }

    /**
     * Implement this function to initialize the entity type.
     * Important: must be the name of the table.
     *
     * @return string Entity type/table name.
     */
    protected abstract function initializeEntityType(): string;

    /**
     * Implement this function to initialize the attributes of the new entity.
     * Important: the first value should be the id.
     *
     * @return array Attribute map.
     */
    protected abstract function initializeAttributes(): array;

    /**
     * Implement this function to create constraints on attributes.
     * Throw exceptions for constraints.
     */
    protected abstract function checkConstraints();

    public function getValue(string $attr)
    {
        $this->checkAttribute($attr);
        return $this->attributes[$attr];
    }

    public function setValue(string $attr, $value)
    {
        $this->checkAttribute($attr);
        $this->attributes[$attr] = $value;
    }

    /**
     * Store a new entity to the database.
     *
     * @return string Error message.
     */
    public function store(): string
    {
        $this->checkConstraints();
        $attrNames = "";
        $insertQuery = "";
        foreach ($this->attributeNames as $attribute) {
            $value = $this->getValue($attribute);
            if (empty($value)) {
                // if value is empty, don't write it to the database
                continue;
            }
            // string values must have '...' in sql:
            if (is_string($value)) {
                $insertQuery .= "'$value'";
            } else {
                $insertQuery .= "$value";
            }
            // add comma between values:
            $attrNames .= "$attribute, ";
            $insertQuery .= ", ";
        }
        // remove last comma:
        $attrNames = rtrim(trim($attrNames), ',');
        $insertQuery = rtrim(trim($insertQuery), ',');
        return $this->model->insert($this->entityType, $attrNames, $insertQuery);
    }

    /**
     * Checks if an attribute exists for the entity.
     *
     * @param
     *            attr The name of the attribute.
     */
    private function checkAttribute($attr)
    {
        if (! in_array($attr, $this->attributeNames)) {
            throw new BadFunctionCallException("Attribute '$attr' doesn't exist for the entity type '$this->entityType'.");
        }
    }
}