<!DOCTYPE html>
<html>

<?php include 'partials/header.php'; ?>

<body>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="container">
                    <div class="p-5 bg-light mb-4 card">
                        <?php if (isset($_GET['success']) && $_GET['success'] == 1) { ?>
                            <p style="color: green;">Task added successfully!</p>
                        <?php } ?>

                        <?php if (isset($_GET['error']) && $_GET['error'] == 1) { ?>
                            <p style="color: red;">Error adding task. Please try again.</p>
                        <?php } ?>

                        <form method="POST" action="process_task.php">
                            <div class="row gap-1">
                                <div class="col">
                                    <label>Task:</label>
                                    <input type="text" name="task" required class="form-control"><br>
                                </div>
                                <div class="col">
                                    <label>Description:</label>
                                    <textarea name="description" class="form-control"></textarea><br>
                                </div>

                            </div>
                            <div class="row gap-1">
                                <div class="col">
                                    <label>Due Date:</label>
                                    <input type="date" name="due_date" required class="form-control"><br>
                                </div>
                                <div class="col">
                                    <label>Due Time:</label>
                                    <input type="time" name="due_time" class="form-control"><br>
                                </div>
                            </div>
                            <div class="row gap-1">
                                <div class="col">
                                    <label>Priority:</label>
                                    <select name="priority" class="form-control">
                                        <option value="high">High</option>
                                        <option value="medium">Medium</option>
                                        <option value="low">Low</option>
                                    </select><br>
                                </div>
                                <div class="col">
                                    <label>Project/Category:</label>
                                    <select name="project" class="form-control">
                                        <option value="personal">Personal</option>
                                        <option value="work">Work</option>
                                        <option value="family">Family</option>
                                    </select><br>
                                </div>
                            </div>
                            <div class="row gap-1">
                                <div class="col">
                                    <label>Labels:</label>
                                    <br>
                                    <input type="checkbox" name="labels[]" value="urgent" class="form-checkbox">
                                    Urgent<br>
                                    <input type="checkbox" name="labels[]" value="important" class="form-checkbox">
                                    Important<br>
                                    <input type="checkbox" name="labels[]" value="home" class="form-checkbox"> Home<br>
                                </div>
                                <div class="col">
                                    <label>Assignee:</label>
                                    <input type="text" name="assignee" class="form-control"><br>
                                </div>
                            </div>
                            <input type="submit" value="Add Task" class="btn btn-primary">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const labelInput = document.getElementById('label');
        const addLabelBtn = document.getElementById('add-label-btn');
        const labelContainer = document.getElementById('label-container');

        addLabelBtn.addEventListener('click', function() {
            const label = labelInput.value.trim();

            if (label !== '') {
                const labelElem = document.createElement('div');
                labelElem.classList.add('label');
                labelElem.innerHTML = `${label}<button type="button" class="remove-label-btn"><i class="fas fa-times"></i></button>`;
                labelContainer.appendChild(labelElem);

                const removeLabelBtns = document.querySelectorAll('.remove-label-btn');
                removeLabelBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        btn.parentElement.remove();
                    });
                });

                labelInput.value = '';
            }
        });

        const priorityButtons = document.querySelectorAll('.priority');
        const priorityInput = document.getElementById('priority');

        priorityButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                priorityButtons.forEach(b => b.classList.remove('priority-selected'));
                btn.classList.add('priority-selected');
                priorityInput.value = btn.dataset.priority;
            });
        });
    </script>




    <?php include 'partials/footer.php'; ?>