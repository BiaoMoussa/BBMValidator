<?php
/**
 * Validateur de formulaire
 *
 * @author Biao Moussa +22789532129
 * @copyright 2isoft
 * @license MIT
 *
 */
class BBMValidator
{
    /**
     * @property array $params
     */
    private array $params;

    /**
     * @var string[]
     */
    private array $errors = [];


    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Vérifie que les champs sont présents dans le tableau
     * @param string ...$keys
     * @return $this
     */
    public function required(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value)) {
                $this->addError($key, 'required');
            }
        }
        return $this;
    }


    /**
     * Vérifie que le champ n'est pas vide
     * @param string ...$keys
     * @return $this
     */
    public function notEmpty(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value) || empty($value)) {
                $this->addError($key, 'notEmpty');
            }
        }
        return $this;
    }

    /**
     * @param string $key
     * @param int|null $min
     * @param int|null $max
     * @return $this
     */
    public function length(string $key, ?int $min, ?int $max = null): self
    {
        $value = $this->getValue($key);
        $length = strlen($value);
        if (!is_null($max) && $length > $max) {
            $this->addError($key, 'maxLength', [$max]);
        }

        if (!is_null($min) && $length < $min) {
            $this->addError($key, 'minLength', [$min]);
        }

        if (!is_null($min) &&
            !is_null($max) &&
            ($length < $min || $length > $max)
        ) {
            $this->addError($key, 'betweenLength', [$min, $max]);
        }
        return $this;
    }


    /**
     * @param string $key
     * @param string $format
     * @return $this
     */
    public function dateTime(string $key, string $format = 'Y-m-d H:i:s'): self
    {
        $value = $this->getValue($key);
        $date = DateTime::createFromFormat($format, $value);
        $errors = DateTime::getLastErrors();
        if ($errors['error_count'] > 0 || $errors['warning_count'] > 0 || $date == false) {
            $this->addError($key, 'datetime', [$format]);
        }
        return $this;
    }


    /**
     * Récupère les erreurs
     * @return string[]
     */
    public function getErrors(): array
    {

        return $this->errors;
    }

    /**
     * @param string $key
     * @return self
     */
    public function slug(string $key): self
    {
        $value = $this->getValue($key);
        $pattern = '/^([a-z0-9]+-?)+$/';
        if (!is_null($value) && !preg_match($pattern, $value)) {
            $this->addError($key, 'slug');
        }

        return $this;
    }

    /**
     * Vérifie le numéro de téléphone
     * @param string $key
     * @return $this
     */
    public function phone(string $key): self
    {
        $value = $this->getValue($key);
        $pattern = '#^([-_/ ]?[0-9]{2}){4}$#';
        if (!is_null($value) && !preg_match($pattern, $value)) {
            $this->addError($key, 'phone');
        }

        return $this;
    }

    /**
     * Vérifie un email
     * @param string $key
     * @return $this
     */
    public function email(string $key): self
    {
        $value = $this->getValue($key);
        $pattern = '/^(([^<>()\[\].,;:\s@"]+(\.[^<>()\[\].,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))/i';
        if (!is_null($value) && !preg_match($pattern, $value)) {
            $this->addError($key, 'email');
        }

        return $this;
    }

    /**
     * @param string ...$keys
     * @return $this
     */
    public function number(string ...$keys): self
    {

        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (!is_numeric($value)) {
                $this->addError($key, 'number');
            }
        }
        return $this;
    }

    /**
     * @param string $key
     * @param array $values
     * @return $this
     */
    public function enum(string $key, array $values): self
    {
        if (!empty($values)) {
            if (!in_array($this->getValue($key), $values)) {
                $this->addError($key, 'enum', [json_encode($values)]);
            }
        }

        return $this;
    }

    /**
     * @param string $key
     * @param int|null $min
     * @param int|null $max
     * @return $this
     */
    public function between(string $key, ?int $min, int $max = null): self
    {
        // La valeur doit être numérique
        $this->number($key);
        $value = $this->getValue($key);
        if (!is_null($max) && $value > $max) {
            $this->addError($key, 'max', [$max]);
        }

        if (!is_null($min) && $value < $min) {
            $this->addError($key, 'min', [$min]);
        }

        if (!is_null($min) &&
            !is_null($max) &&
            ($max >= $min) &&
            ($value < $min || $value > $max)
        ) {
            $this->addError($key, 'between', [$min, $max]);
        }
        return $this;
    }

    /**
     * @param string $key
     * @param string $pattern
     * @return $this
     */
    public function match(string $key,string $pattern):self
    {
        $value = $this->getValue($key);
        if (!is_null($value) && !preg_match($pattern, $value)) {
            $this->addError($key, 'match');
        }
        return $this;
    }

    /**
     * Vérifie si les données sont valides
     * @return bool
     * @throws Exception
     */
    public function isValid(): bool
    {
        if (!empty($this->errors)) throw new Exception($this->errors[0]);
        return true;
    }


    /**
     * Ajoute une erreur
     * @param string $key
     * @param string $rule
     * @param array|null $attributes
     * @return void
     */
    private function addError(string $key, string $rule, ?array $attributes = []): void
    {
        $this->errors[] = new ValidationError($key, $rule, $attributes);
    }

    /**
     * Récupère les valeurs
     * @param string $key
     * @return mixed|null
     */
    private function getValue(string $key): mixed
    {

        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return null;
    }
}


/**
 * Class pour les erreurs
 */
class ValidationError
{
    /**
     * @var string
     */
    private string $key;
    /**
     * @var string
     */
    private string $rule;
    /**
     * @var string[]
     */
    public array $message = [
        'required' => 'Le champs %s est réquis',
        'notEmpty' => 'Le champs %s ne peut être vide',
        'slug' => 'Le champs %s n\'est pas un slug valide',
        'minLength' => 'Le champs %s doit contenir plus de %d caractères',
        'maxLength' => 'Le champs %s doit contenir moins de %d caractères',
        'betweenLength' => 'Le champs %s doit contenir entre %d et %d caractères',
        'min' => 'Le champs %s doit être supérieur à  %d',
        'max' => 'Le champs %s doit être inférieur à %d',
        'between' => 'Le champs %s doit être compris entre %d et %d',
        'datetime' => 'Le champs %s doit être une date valide au format (%s)',
        'phone' => 'Le champs %s n\'est pas un numéro de téléphone valide',
        'email' => 'Le champs %s n\'est pas un email valide',
        'number' => 'Le champs %s n\'est pas un nombre valide',
        'enum' => 'Le champs %s doit être parmi %s',
        'match' => 'Le champs %s n\'est pas valide',
    ];
    /**
     * @var array
     */
    private array $attributes;

    /**
     * @param string $key
     * @param string $rule
     * @param array $attributes
     */
    public function __construct(string $key, string $rule, array $attributes = [])
    {
        $this->key = $key;
        $this->rule = $rule;
        $this->attributes = $attributes;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $params = array_merge([$this->message[$this->rule], $this->key], $this->attributes);
        return (string)call_user_func_array('sprintf', $params);
    }
}