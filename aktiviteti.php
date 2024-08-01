<?php
include 'partials/header.php';
include 'conn-d.php';

?>
<style>
    .fc * {
        text-transform: capitalize !important;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var employeeSelect = document.getElementById('employeeSelect');
        var seleceventDidMounttedEmployeeId = '';
        var selectedEmployeeEmail = '';
        employeeSelect.addEventListener('change', function() {
            selectedEmployeeId = this.value;
            selectedEmployeeEmail = this.options[this.selectedIndex].getAttribute('data-email');
        });
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'sq',
            themeSystem: 'bootstrap5',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            buttonText: {
                today: 'Sot',
                month: 'Muaji',
                week: 'Java',
                day: 'Dita',
                list: 'Lista'
            },
            events: {
                url: 'fetch-events.php',
                failure: function() {
                    Swal.fire('Error!', 'Ka pasur problem me marrjen e ngjarjeve.', 'error');
                },
            },
            editable: true,
            selectable: true,
            nowIndicator: true,
            dayMaxEvents: true,
            eventTimeFormat: {
                hour: 'numeric',
                minute: '2-digit',
                meridiem: 'short'
            },
            // Event Click Handler
            eventClick: function(info) {
                Swal.fire({
                    title: 'Edito ' + info.event.title,
                    html: `
            <div class="form-group">
                <label for="editLeaveTitle" class="col-form-label">Titulli i kërkesës</label>
                <input id="editLeaveTitle" class="form-control" value="${info.event.title}" placeholder="Titulli i kërkesës">
            </div>
            <div class="row">
            <div class="form-group col">
                <label for="editLeaveStart" class="col-form-label">Data e Fillimit</label>
                <input id="editLeaveStart" class="form-control flatpickr" type="text" value="${info.event.startStr.split('T')[0]}" autocomplete="off">
            </div>
            <div class="form-group col">
                <label for="editLeaveEnd" class="col-form-label">Data e Përfundimit</label>
                <input id="editLeaveEnd" class="form-control flatpickr" type="text" value="${info.event.end ? info.event.endStr.split('T')[0] : info.event.startStr.split('T')[0]}" autocomplete="off">
            </div>
            </div>
            <div class="form-group">
                <label for="editLeaveStatus" class="col-form-label">Statusi</label>
                <select id="editLeaveStatus" class="form-control">
                    <option value="Në pritje" ${info.event.extendedProps.status === 'Në pritje' ? 'selected' : ''}>Në pritje</option>
                    <option value="Aprovuar" ${info.event.extendedProps.status === 'Aprovuar' ? 'selected' : ''}>Aprovuar</option>
                    <option value="Refuzuar" ${info.event.extendedProps.status === 'Refuzuar' ? 'selected' : ''}>Refuzuar</option>
                </select>
            </div>
        `,
                    focusConfirm: false,
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    cancelButtonText: 'Cancel',
                    preConfirm: () => {
                        return {
                            id: info.event.id,
                            title: document.getElementById('editLeaveTitle').value,
                            start_date: document.getElementById('editLeaveStart').value,
                            end_date: document.getElementById('editLeaveEnd').value,
                            status: document.getElementById('editLeaveStatus').value
                        };
                    },
                    didOpen: () => {
                        // Initialize flatpickr for start and end date inputs
                        const datepickrs = Swal.getPopup().querySelectorAll('.flatpickr');
                        datepickrs.forEach(datepickr => {
                            flatpickr(datepickr, {
                                dateFormat: 'Y-m-d',
                                allowInput: true,
                                disableMobile: true // Optional: disable mobile-friendly UI
                            });
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateEvent(result.value, info.event);
                    }
                });
            },
            // Select Handler
            select: function(info) {
                if (!selectedEmployeeId) {
                    Swal.fire('Error', 'Ju lutem zgjedhni një punonjës së pari.', 'error');
                    return;
                }
                Swal.fire({
                    title: 'Shto kërkes të re',
                    html: `
                    <div class="form-group">
                        <label for="leaveTitle" class="col-form-label">Titulli i kërkesës</label>
                        <input id="leaveTitle" class="form-control" placeholder="Titulli i kërkesës">
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label for="leaveStart" class="col-form-label">Data e fillimit</label>
                            <input id="leaveStart" class="form-control flatpickr" type="text" value="${info.startStr}" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="leaveEnd" class="col-form-label">Data e përfundimit</label>
                            <input id="leaveEnd" class="form-control flatpickr" type="text" value="${info.endStr}" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="leaveStatus" class="col-form-label">Statusi</label>
                        <select id="leaveStatus" class="form-select">
                            <option value="Në pritje">Në pritje</option>
                            <option value="Aprovuar">Aprovuar</option>
                            <option value="Refuzuar">Refuzuar</option>
                        </select>
                    </div>
                `,
                    focusConfirm: false,
                    preConfirm: () => {
                        return {
                            title: document.getElementById('leaveTitle').value,
                            start_date: document.getElementById('leaveStart').value,
                            end_date: document.getElementById('leaveEnd').value,
                            status: document.getElementById('leaveStatus').value,
                            employee_id: selectedEmployeeId,
                            employee_email: selectedEmployeeEmail
                        };
                    },
                    didOpen: () => {
                        // Initialize flatpickr for start and end date inputs
                        const datepickrs = Swal.getPopup().querySelectorAll('.flatpickr');
                        datepickrs.forEach(datepickr => {
                            flatpickr(datepickr, {
                                dateFormat: 'Y-m-d',
                                allowInput: true,
                                disableMobile: true
                            });
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send leave request to add-leave.php
                        fetch('add-leave.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: new URLSearchParams(result.value)
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Sukses', data.message, 'success');
                                    calendar.addEvent({
                                        title: result.value.title,
                                        start: result.value.start_date,
                                        end: result.value.end_date,
                                        extendedProps: {
                                            status: result.value.status,
                                            employeeId: result.value.employee_id,
                                            employeeEmail: result.value.employee_email
                                        }
                                    });
                                } else {
                                    Swal.fire('Error', data.message, 'error');
                                }
                            })
                            .catch((error) => {
                                console.error('Error:', error);
                                Swal.fire('Error', 'Failed to add leave request', 'error');
                            });
                    }
                });
            },
            eventDrop: function(info) {
                console.log('Event moved:', info.event.title, info.event.start.toISOString());
                // Here you would update the event in your database
            },
            eventResize: function(info) {
                console.log('Event resized:', info.event.title, info.event.start.toISOString(), info.event.end.toISOString());
                // Here you would update the event in your database
            },
            loading: function(isLoading) {
                if (isLoading) {
                    Swal.fire({
                        title: 'Loading events...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                } else {
                    Swal.close();
                }
            },
            eventDidMount: function(info) {
                new bootstrap.Tooltip(info.el, {
                    title: `${info.event.title}<br>
                            Start: ${info.event.start.toLocaleDateString()}<br>
                            End: ${info.event.end ? info.event.end.toLocaleDateString() : 'N/A'}<br>
                            Status: ${info.event.extendedProps.status || 'N/A'}`,
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body',
                    html: true
                });
                info.el.style.textTransform = 'capitalize';

            },
            datesSet: function(dateInfo) {
                var events = calendar.getEvents();
                var visibleEvents = events.filter(event =>
                    event.start >= dateInfo.start && event.start <= dateInfo.end
                );
                // document.getElementById('eventSummary').textContent = `Visible events: ${visibleEvents.length}`;
            }
        });
        calendar.render();

        function updateEvent(updatedData, event) {
            fetch('update-leave.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(updatedData)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success', 'Kërkesa u përditësua me sukses.', 'success');
                        event.remove();
                        calendar.addEvent({
                            id: updatedData.id,
                            title: updatedData.title,
                            start: updatedData.start_date,
                            end: updatedData.end_date,
                            extendedProps: {
                                status: updatedData.status
                            }
                        });
                    } else {
                        Swal.fire('Error', data.message || 'Failed to update leave request', 'error');
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Failed to update leave request: ' + error.message, 'error');
                });
        }
    });
</script>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="p-5 rounded-5 shadow-sm mb-4 card">
                <nav class="bg-white px-2 rounded-5" style="width:fit-content;" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Menaxhimi</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="text-reset" style="text-decoration: none;">Aktiviteti</a>
                        </li>
                    </ol>
                </nav>
                <?php

                // GET THE email from cookie

                if ($user_info['email'] == 'egjini17@gmail.com') : ?>
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <div class="card shadow-sm rounded-4 p-4 h-100">
                                <div class="mb-3">
                                    <label for="employeeSelect" class="form-label">Zgjidh Punonjësin</label>
                                    <select id="employeeSelect" class="form-control">
                                        <option value="">Zgjidh një punonjës</option>
                                        <?php
                                        include 'conn-d.php';
                                        $sql = "SELECT id, firstName, last_name, email FROM googleauth";
                                        $result = $conn->query($sql);
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='{$row['id']}' data-email='{$row['email']}'>{$row['firstName']} {$row['last_name']} ({$row['email']})</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div id='calendar' class="w-100"></div>
                            </div>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="row">
                        <h4 class="mb-4">Kërko ditë të lirë</h4>
                        <form id="leaveRequestForm" action="request-leave.php" method="POST">
                            <div class="mb-3">
                                <label for="leaveTitle" class="form-label">Arsyeja e kërkesës</label>
                                <input type="text" class="form-control" id="leaveTitle" name="title" required>
                            </div>
                            <div class="row">
                                <div class="mb-3 col">
                                    <label for="leaveStart" class="form-label">Data e Fillimit</label>
                                    <input type="date" class="form-control" id="leaveStart" name="start_date" required>
                                </div>
                                <div class="mb-3 col">
                                    <label for="leaveEnd" class="form-label">Data e Përfundimit</label>
                                    <input type="date" class="form-control" id="leaveEnd" name="end_date" required>
                                </div>
                            </div>
                            <button type="submit" class="input-custom-css px-3 py-2">Dërgo Kërkesën</button>
                        </form>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include 'partials/footer.php'; ?>