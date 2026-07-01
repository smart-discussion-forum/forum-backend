@extends('layouts.app')
@section('content')
    <div class="screen-title">Quiz configuration</div>
    <form method="POST" action="/quizzes">
        @csrf
        <label>Quiz Title:</label>
        <input type="text" name="title">
        <label>Date:</label>
        <input type="datetime-local" name="start_time">
        <label>Duration (minutes):</label>
        <input type="number" name="duration_minutes">
        <label>Category:</label>
        <select name="target_category">
            <option value="">All</option>
            <option value="year1">Year 1</option>
            <option value="year2">Year 2</option>
            <option value="year3">Year 3</option>
        </select>
        <label>Questions (one per line — format: question | optionA,optionB,optionC | correct option index 0-2):</label>
        <textarea name="raw_questions" rows="4" placeholder="2+2=? | 3,4,5 | 1"></textarea>
        <div style="text-align:right; margin-top:10px;">
            <button type="submit" class="btn">save</button>
        </div>
    </form>
@endsection
