<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ToDoList extends Model
{
    protected $fillable = [
		'to_do',
		'is_done',
	];
}
