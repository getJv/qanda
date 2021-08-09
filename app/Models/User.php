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

    /**
     * Provide a list of questions and answer
     * in table format for Qanda command
     *
     * @return array
     */
    public function listOfQuestionAndAnswers(): array
    {

        $headers = ['Question',"Answer"];
        $rows = [];
        foreach ($this->questions->fresh() as $question){
            $rows[] = [
                $question->title,
                $question->answer,
            ];
        }
        return [$headers,$rows];
    }
    /**
     * Provide a list of questions and stats
     * in table format for Qanda command
     *
     * @return array
     */
    public function listOfQuestionAndStats(){

        $headers = ['ID', 'Question',"Last answer"];
        $statusText = function($value){
            $text = "Not answered";
            if($value === 'C'){
                $text = "Correct";
            }else if($value === 'W'){
                $text = "Incorrect";
            }
            return $text;
        };
        $rows = [];
        foreach ($this->questions->fresh() as $question){
            $rows[] = [
                $question->id,
                $question->title,
                $question->answer,
                $statusText($question->last_answer)
            ];
        }
        return [$headers,$rows];
    }
    /**
     * Return the question Stats for Qanda command
     * @return array
     */
    public static function listOfUsers(){
        $users = User::all();
        $userList = [];
        foreach ($users as $user){
            $userList[] = ["method" => '', 'title' => $user->name ];
        }
        return $userList;
    }

    /**
     * Return the question Stats for Qanda command
     * @return array
     */
    public function questionStats(){
        $totalOfQuestion = $this->questions()->count();
        $totalOfCorrectAnswers = $this->questions()->where('last_answer','C')->count();
        $totalOfInCorrectAnswers = $this->questions()->where('last_answer','W')->count();
        $totalOfNotAnswers = $this->questions()->where('last_answer',null)->count();
        $percentage = round(floatval( $totalOfCorrectAnswers/$totalOfQuestion) * 100,2);
        $message = "Score: {$percentage}%. You did {$totalOfCorrectAnswers} of {$totalOfQuestion}.";
        return [$message,$totalOfQuestion,$totalOfCorrectAnswers,$totalOfInCorrectAnswers,$totalOfNotAnswers,$percentage];
    }
}
