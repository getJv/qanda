<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function questions(){
        return $this->hasMany(Question::class);
    }
    public function listOfQuestions(){

        $statusText = function($value){
            $text = "Not answered";
            if($value === 'C'){
                $text = "Correct";
            }else if($value === 'W'){
                $text = "Incorrect";
            }
            return $text;
        };
        $questionList = [];
        foreach ($this->questions->fresh() as $question){
            $questionList[] = [
                $question->id,
                $question->title,
                $question->answer,
                $statusText($question->last_answer)
            ];
        }
        return $questionList;
    }

    public static function listOfUsers(){
        $users = User::all();
        $userList = [];
        foreach ($users as $user){
            $userList[] = ["method" => '', 'title' => $user->name ];
        }
        return $userList;
    }

    public function questionStats(){
        $totalOfQuestion = $this->questions()->count();
        $totalOfCorrectAnswers = $this->questions()->where('last_answer','C')->count();
        $totalOfInCorrectAnswers = $this->questions()->where('last_answer','W')->count();
        $totalOfNotAnswers = $this->questions()->where('last_answer',null)->count();
        $percentage = (floatval( $totalOfCorrectAnswers/$totalOfQuestion)) * 100;
        $message = "Score: {$percentage}%. You did {$totalOfCorrectAnswers} of {$totalOfQuestion}.";

        return [$message,$totalOfQuestion,$totalOfCorrectAnswers,$totalOfInCorrectAnswers,$totalOfNotAnswers,$percentage];
    }
}
