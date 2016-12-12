<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 12/7/16
 * Time: 10:23 AM
 */

namespace App\Models\ModelTraits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\Relation;

trait Deletable
{
    private $properties = [];
    private $skipMethods = [];
    protected function getPropertiesFromMethods()
    {
        $methods = get_class_methods($this);
        if ($methods) {
            foreach ($methods as $method) {
                //there are system items that really dont need to be displayed
                if (in_array($method, $this->skipMethods)) {
                    continue;
                }
                if (!method_exists('Illuminate\Database\Eloquent\Model', $method)
                    && !Str::startsWith($method, 'get')
                ) {
                    //Use reflection to inspect the code, based on Illuminate/Support/SerializableClosure.php
                    $reflection = new \ReflectionMethod($this, $method);
                    $file = new \SplFileObject($reflection->getFileName());
                    $file->seek($reflection->getStartLine() - 1);
                    $code = '';
                    while ($file->key() < $reflection->getEndLine()) {
                        $code .= $file->current();
                        $file->next();
                    }
                    $code = trim(preg_replace('/\s\s+/', '', $code));
                    $begin = strpos($code, 'function(');
                    $code = substr($code, $begin, strrpos($code, '}') - $begin + 1);
                    foreach (array(
                                 'hasMany',
                                 'hasManyThrough',
                                 'belongsToMany',//skipping hasOne because usually we dont need to handle that
                                 'belongsTo',
                                 'morphOne',
                                 'morphTo',
                                 'morphMany',
                                 'morphToMany'
                             ) as $relation) {

                        $search = '$this->' . $relation . '(';
                        if ($pos = stripos($code, $search)) {
                            //Resolve the relation's model to a Relation object.
                            $relationObj = $this->$method();
                            if ($relationObj instanceof Relation) {
                                $relatedModel = '\\' . get_class($relationObj->getRelated());
                                $relations = ['hasManyThrough', 'belongsToMany', 'hasMany'];
                                if (in_array($relation, $relations)) {

                                    $this->setProperty(
                                        $method,
                                        $this->getCollectionClass($relatedModel) . '|' . $relatedModel . '[]'
                                    );
                                } else {
                                    //Single model is returned
                                    $this->setProperty($method, $relatedModel);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function canModelBeDeleted()
    {
        $this->getPropertiesFromMethods();
        $return = $this->makePretty($this->properties);
        return empty($return) ?  true : $return;

    }

    protected function setProperty($name, $type = null)
    {
        if (!isset($this->properties[$name])) {
            $this->properties[$name] = array();
            $this->properties[$name]['type'] = 'mixed';
        }
        if ($type !== null) {
            $this->properties[$name]['type'] = $type;
        }
    }

    /**
     * Determine a model classes' collection type.
     *
     * @see http://laravel.com/docs/eloquent-collections#custom-collections
     * @param string $className
     * @return string
     */
    private function getCollectionClass($className)
    {
        // Return something in the very very unlikely scenario the model doesn't
        // have a newCollection() method.
        if (!method_exists($className, 'newCollection')) {
            return '\Illuminate\Database\Eloquent\Collection';
        }
        $model = new $className;
        return '\\' . get_class($model->newCollection());
    }

    private function makePretty($options){
        $string = '';
        $class = get_class($this);
        foreach ($options as $method => $relationship) {
            $count = $this->$method->count();
            if($count > 0) {
                $string .= "{$method} has {$count} Rows Attached<br>";
                $data = $this->$method;
                if (is_a($data, '\Illuminate\Database\Eloquent\Collection')) {
                    foreach ($data as $item) {
                        $string .= "{$class} is linked to {$method} ID {$item->id}<br>";
                    }
                } else {
                    $string .= "{$method} - id {$data->id}<br>";
                }
            }
        }
        return $string;
    }

}