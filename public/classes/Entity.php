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

    protected $database;

    public $ID = "id";

    public final function __construct(Database $database)
    {
        $this->database = $database;
        $this->entityType = $this->initializeEntityType();
        $this->attributeNames = $this->initializeAttributes();
        array_push($this->attributeNames, $this->ID);
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
     *
     * @return string Error message or empty string if no constraint was matched.
     */
    protected abstract function checkConstraints(): string;

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
        $message = $this->checkConstraints();
        if (! empty($message)) {
            throw new InvalidArgumentException($message);
        }
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
        $message = $this->database->insert($this->entityType, $attrNames, $insertQuery);
        // set id of inserted entity.
        $this->setValue($this->ID, $this->database->insert_id);
        return $message;
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

    /**
     * Checks if one or more of the attributes are empty.
     *
     * @param array $attributes
     *            Names of the attributes.
     * @return Error message if some are empty.
     */
    public final function isEmpty(array $attributes): string
    {
        $message = "";
        // not nullable:
        foreach ($attributes as $attribute) {
            if (empty($this->getValue($attribute))) {
                $message .= "Attribute '$attribute' is not nullable. ";
            }
        }
        return $message;
    }
}