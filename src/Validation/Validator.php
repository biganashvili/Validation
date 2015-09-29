<?php
namespace KennedyTedesco\Validation;

use Symfony\Component\Translation\TranslatorInterface;
use Illuminate\Validation\Validator as BaseValidator;
use KennedyTedesco\Validation\Respect\Factory as RuleFactory;

class Validator extends BaseValidator
{
    /**
     * Validate a minimum age.
     *
     * @param string $attribute
     * @param string $value
     * @param array  $parameters
     *
     * @return bool
     */
    public function validateMinimumAge($attribute, $value, $parameters)
    {
        return RuleFactory::make('MinimumAge', [empty($parameters[0]) ? 18 : (int) $parameters[0], empty($parameters[1]) ? null : $parameters[1]])->validate($value);
    }

    /**
     * Validate if file exists.
     *
     * @param string $attribute
     * @param string $value
     * @param array  $parameters
     *
     * @return bool
     */
    public function validateFileExists($attribute, $value, $parameters)
    {
        return RuleFactory::make('exists', [])->validate($value);
    }

    /**
     * Replace all error message place-holders with actual values.
     *
     * @param  string  $message
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return string
     */
    protected function doReplacements($message, $attribute, $rule, $parameters)
    {
        $message = parent::doReplacements($message, $attribute, $rule, $parameters);
        $search = [];
        foreach ($parameters as $key => $parameter) {
            array_push($search, ':parameter'.$key);
        }
        return str_replace($search, $parameters, $message);
    }

    /**
     * Handle dynamic calls to class methods.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        try {
            $rule = lcfirst(substr($method, 8));
            $args = $parameters[2];
            $value = $parameters[1];
            $ruleObject = RuleFactory::make($rule, $args);
            return $ruleObject->validate($value);
        } catch (\Exception $e) {
            return parent::__call($method, $parameters);
        }
    }
}
