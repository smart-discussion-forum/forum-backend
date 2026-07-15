@extends('layouts.app')
@section('content')
    <div class="auth-card" style="max-width:900px; margin:24px auto; padding:28px;">
        <div class="screen-title" style="color:var(--text); margin-bottom:8px;">Quiz configuration</div>
        <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap; margin-bottom:18px;">
            <a href="/quizzes" class="dash-btn">Back to Quizzes</a>
            <a href="/dashboard" class="dash-btn">Dashboard</a>
            <a href="/topics" class="dash-btn">Topics</a>
        </div>

        <form method="POST" action="/quizzes" id="quizForm">
            @csrf
            <label>Quiz Title:</label>
            <input type="text" name="title" required>

            <label>Date:</label>
            <input type="datetime-local" name="start_time" required>

            <label>Duration (minutes):</label>
            <input type="number" name="duration_minutes" min="1" required>

            <label>Group:</label>
            <select name="group_id" required>
                <option value="">-- Select a group --</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </select>

            <div style="margin-top:20px;">
                <label style="font-weight:700;">Questions:</label>
                <div id="questionsContainer"></div>
                <button type="button" id="addQuestionBtn" class="dash-btn" style="margin-top:10px;">+ Add Question</button>
            </div>

            <div style="text-align:right; margin-top:20px;">
                <button type="submit" class="btn">Save Quiz</button>
            </div>
        </form>
    </div>

    <template id="questionTemplate">
        <div class="panel question-block" style="margin-top:16px; padding:16px; border:1px solid #d8dbe2; border-radius:8px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <label style="font-weight:600;">Question <span class="question-number"></span></label>
                <button type="button" class="remove-question-btn dash-btn" style="padding:2px 10px;">Remove</button>
            </div>
            <input type="text" class="question-text" placeholder="Enter question text" required>

            <div class="options-container"></div>
            <button type="button" class="add-option-btn dash-btn" style="margin-top:6px;">+ Add Option</button>

            <label style="margin-top:10px;">Marks for this question:</label>
            <input type="number" class="question-marks" value="1" min="1" style="max-width:100px;">
        </div>
    </template>

    <template id="optionTemplate">
        <label style="display:flex; align-items:center; gap:8px; margin-top:6px;">
            <input type="radio" class="correct-option-radio" style="width:auto;">
            <input type="text" class="option-text" placeholder="Option text" required style="flex:1;">
            <button type="button" class="remove-option-btn dash-btn" style="padding:2px 8px;">✕</button>
        </label>
    </template>

    <script>
        let questionCount = 0;

        function addQuestion() {
            questionCount++;
            const template = document.getElementById('questionTemplate').content.cloneNode(true);
            const block = template.querySelector('.question-block');
            block.dataset.index = questionCount;
            block.querySelector('.question-number').textContent = questionCount;

            block.querySelector('.remove-question-btn').addEventListener('click', () => {
                block.remove();
            });

            block.querySelector('.add-option-btn').addEventListener('click', () => {
                addOption(block);
            });

            document.getElementById('questionsContainer').appendChild(block);

            addOption(block);
            addOption(block);
        }

        function addOption(questionBlock) {
            const optionsContainer = questionBlock.querySelector('.options-container');
            const radioName = 'correct_' + questionBlock.dataset.index;

            const template = document.getElementById('optionTemplate').content.cloneNode(true);
            const label = template.querySelector('label');
            const radio = template.querySelector('.correct-option-radio');
            radio.name = radioName;

            label.querySelector('.remove-option-btn').addEventListener('click', () => {
                label.remove();
            });

            optionsContainer.appendChild(template);
        }

        document.getElementById('addQuestionBtn').addEventListener('click', addQuestion);

        addQuestion();

        document.getElementById('quizForm').addEventListener('submit', function (e) {
            const blocks = document.querySelectorAll('.question-block');
            const lines = [];

            blocks.forEach(block => {
                const questionText = block.querySelector('.question-text').value.trim();
                const marks = block.querySelector('.question-marks').value || 1;
                const optionLabels = block.querySelectorAll('.option-text');
                const optionRadios = block.querySelectorAll('.correct-option-radio');

                const options = [];
                let correctIndex = null;

                optionLabels.forEach((input, i) => {
                    options.push(input.value.trim());
                    if (optionRadios[i].checked) {
                        correctIndex = i;
                    }
                });

                if (questionText && options.length >= 2 && correctIndex !== null) {
                    lines.push(questionText + ' | ' + options.join(',') + ' | ' + correctIndex + ' | ' + marks);
                }
            });

            if (lines.length === 0) {
                alert('Please add at least one complete question with a marked correct answer.');
                e.preventDefault();
                return;
            }

            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'raw_questions';
            hiddenInput.value = lines.join('\n');
            this.appendChild(hiddenInput);
        });
    </script>
@endsection