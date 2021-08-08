<?php


namespace App\Repositories\Source;


use App\Models\ORM\Source;

class SourceRepository implements ISourceRepository
{
    protected $source = null;

    public function __construct(Source $source)
    {
        $this->source = $source;
    }

    public function create($data)
    {
        $source = new Source($data);
        $source->save();
        return $source;
    }

    public function get($id)
    {
        return $this->source::find($id);
    }

    public function getWith($relation)
    {
        return $this->source::with($relation)->get();
    }

    public function delete(&$self)
    {
        $self->delete();
    }

    public function save(&$self)
    {
        $self->save();
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->source, $method], $args);
    }
}