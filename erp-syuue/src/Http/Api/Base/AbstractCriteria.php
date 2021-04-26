<?php
namespace Newbee\Syuue\Http\Api\Base;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

abstract class AbstractCriteria implements CriteriaInterface
{
    protected $orCondition;
    protected $andCondition;
    protected $orders;
    protected $fieldCondition;
    protected $fieldStorage;

    public function __construct($orCondition= [], $andCondition = [], $orders = [], $fieldCondition = [], $fieldStorage = [])
    {
        $this->orCondition = $orCondition;
        $this->andCondition = $andCondition;
        $this->fieldCondition = $fieldCondition;
        $this->orders = $orders;
        $this->fieldStorage = $fieldStorage;
    }

    public function apply($model, RepositoryInterface $repository)
    {
        // TODO: Implement apply() method.
        $tableName = $model->getTable();
        $model = $this->order($model, $tableName);
        foreach ($this->andCondition as $field=>$condition) {
            $model = $model->where($tableName.".".$field, $condition);
        }
        $model = $model->where(function ($query) use ($tableName){
            foreach ($this->orCondition as $field => $condition){
                $query->orWhere($tableName.".".$field, 'like', "%$condition%");
            }
        });
        $model = $this->whereField($model, $tableName);
        return $model;
    }
    protected function order($model, $tableName)
    {
        empty($this->orders) && $this->orders[(new $model)::CREATED_AT] = 'desc';
        foreach ($this->orders as $field => $order) {
            $model = $model->orderBy($tableName.'.'.$field, $order);
        }
        if($tableName == 'locus'){
            $model = $model->groupBy('waybill_no');
        }
        return $model;
    }
    protected function whereField($model, $tableName)
    {
        $table = substr($tableName,strripos($tableName,"_")+1);
        foreach ($this->fieldStorage as $k => &$v){
            if (isset($this->fieldCondition[$k]))
            {
                switch ($this->fieldCondition[$k])
                {
                    //内表模糊查询
                    case 'likes' :
                        $split = explode('__', $k);
                        $relation = reset($split);
                        $field = end($split);
                        $model = $model->whereHas($relation, function (Builder $query) use ($v, $field, $tableName) {
                            $query->where($field, 'like', '%'.$v.'%');
                        });
                        break;
                    //外表模糊查询
                    case 'like' :
                            $model->where($k, 'like', '%'.$v.'%');
                        break;
                    //大于
                    case '>' :
                        !empty($v) && $model = $model->where($k, '>', $v);
                        break;
                    //小于
                    case '<' :
                        !empty($v) && $model = $model->where($k, '<', $v);
                        break;
                    //大于等于
                    case '>=' :
                        if ($k == 'distribution__insert_time')
                        {
                            $field = 'distribution_end_at';
                            $model = $model->whereHas('distribution', function (Builder $query) use ($v, $field, $tableName) {
                                $query->where($field, '>=', $v);
                            });
                        } elseif ($k == 'distribution__insert_at'){
                            $field = 'distribution_insert_at';
                            $model = $model->whereHas('distribution', function (Builder $query) use ($v, $field, $tableName) {
                                $query->where($field, '>=', $v);
                            });
                        } else{
                            !empty($v) && $model = $model->where($table.'_'.$k, '>=', $v);
                        }
                        break;
                    //小于等于
                    case '<=' :
                        if ($k == 'distribution__end_time')
                        {
                            $field = 'distribution_end_at';
                            $model = $model->whereHas('distribution', function (Builder $query) use ($v, $field, $tableName) {
                                $query->where($field, '<=', $v);
                            });
                        }elseif ($k == 'distribution__end_at'){
                            $field = 'distribution_end_at';
                            $model = $model->whereHas('distribution', function (Builder $query) use ($v, $field, $tableName) {
                                $query->where($field, '<=', $v);
                            });
                        } else {
                            !empty($v) && $model = $model->where($table . '_' . $k, '<=', $v);
                        }
                        break;
                    //区间
                    case 'between' :
                        if (strpos($k,'__') !== false){
                            $fields = explode('__', $k);
                            $list = json_decode($v,true);
                            !empty($list[0]) && $model = $model->where($fields[0], '>=', $list[0]);
                            !empty($list[1]) && $model = $model->where($fields[1], '<=', $list[1]);
                        }else{
                            $list = json_decode($v,true);
                            if (empty($list[0]) || empty($list[1])){
                                !empty($list[0]) && $model = $model->where($k, '>=', $list[0]);
                                !empty($list[1]) && $model = $model->where($k, '<=', $list[1]);
                            }else{
                                $model = $model->WhereBetween($k, [$list[0], $list[1]]);
                            }
                        }
                        break;
                    //外表查询
                    case '=' :
                        $split = explode('__', $k);
                        $relation = reset($split);
                        $field = end($split);
                        $model = $model->whereHas($relation, function (Builder $query) use ($v, $field, $tableName) {
                            $query->where($field, $v);
                        });
                        break;
                    //差异查询
                    case '?' :
                        if ($v > 0)
                        {
                            $model = $model->where($k, '>', 0);
                        }else{
                            $model = $model->where($k, '=', $v);
                        }
                        break;
                    default :;
                }
            }
        }
        return $model;
    }
}
