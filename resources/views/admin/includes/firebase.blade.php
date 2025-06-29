


<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
    <div id="toastNotification"
         style="display: none; background: rgba(244, 205, 237, 0.8); width: 350px; height: 100px; border-radius: 10px; color: rgb(10, 9, 9);"
         aria-live="assertive" aria-atomic="true">
        <div class="toast-header">

            <strong class="me-auto">Order</strong>
            <small class="text-muted">Just now</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"
                    onclick="hideNotificationToast()">X</button>
        </div>
        <div class="toast-body">
            Order saved successfully
        </div>
    </div>
</div>
</div>




{{-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> --}}
<script src="https://www.gstatic.com/firebasejs/8.3.2/firebase.js"></script>


<script>
    function hideNotificationToast() {
        console.log(56);

        const toast = document.getElementById('toastNotification');
        toast.style.opacity = '0'; // Fade out
        setTimeout(() => {
            toast.style.display = 'none'; // Hide after fade-out
        }, 500); // Match the fade-out duration
    }

    $(document).ready(function() {
        if (firebase.messaging.isSupported()) {
            // startFCM();
        } else {
            console.error('This browser does not support Firebase Cloud Messaging.');
        }
        // updateNotifications();
        // console.log('clicke34');

        $(document).on('click', '.mark-as-read', function(event) {
            console.log('clicke34');

            markAsRead();
        });




        // Handle click events inside the dropdown menu to prevent closing the menu
        $('#notificationDropdown').on('click', function(event) {
            event.stopPropagation(); // Prevent event from bubbling up to the dropdown toggle
        });

        // Ensure the dropdown menu remains open when clicking inside
        $('.dropdown-menu').on('click', function(event) {
            event.stopPropagation(); // Prevent event from bubbling up to the dropdown toggle
        });


    })
    var firebaseConfig = {


        apiKey: "AIzaSyDFnoM5nwPdB-43me0sxO5hSysTvrMQxWI",
        authDomain: "alshrouqexpress-97ebd.firebaseapp.com",
        databaseURL: "https://alshrouqexpress-97ebd-default-rtdb.firebaseio.com",
        projectId: "alshrouqexpress-97ebd",
        storageBucket: "alshrouqexpress-97ebd.appspot.com",
        messagingSenderId: "556213764824",
        appId: "1:556213764824:web:29d8ace147869174100dad",
        measurementId: "G-6DKM5SR2XV"
    };


    function updateNotifications() {
        $.ajax({
            url: '{{ route('fetch-notifications') }}',
            type: 'GET',
            success: function(response) {
                $('#notificationDropdown').html(response.html);
            },
            error: function(xhr) {
                console.error('Error fetching notifications:', xhr);
            }
        });
    }

    function markAsRead() {
        console.log('ajax');

        $.ajax({
            url: '{{ route('mark-as-read') }}',
            type: 'POST',
            data: {

                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log(response);

                $('#notifications-count').html(response.count);
                $('#notifications-count2').html(response.count + 'Notifications')
                // updateNotifications();
            },
            error: function(xhr) {
                console.error('Error marking notification as read:', xhr);
            }
        });
    }

    if (firebase.messaging.isSupported()) {
        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();
        console.log(messaging.getToken());

        function startFCM() {
            console.log(23);

            messaging
                .requestPermission()
                .then(function() {
                    return messaging.getToken()
                })
                .then(function(response) {
                    console.log(456);

                    console.log(response);

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: '{{ route('save-firebase-token') }}',
                        type: 'POST',
                        data: {
                            token: response
                        },
                        dataType: 'JSON',
                        success: function(response) {
                            // alert('Token stored.');
                        },
                        error: function(error) {
                            // alert(error);
                        },
                    });
                }).catch(function(error) {
                // alert(error);
            });
        }

        messaging.onMessage(function(payload) {
            const toastElement = $('#toastNotification');
            console.log(toastElement);

            $('#notifications-count').html(payload.data.notification_count);
            $('#notifications-count2').html(payload.data.notification_count + 'Notifications')
            console.log(payload.data.notification_count);

            toastElement.find('.toast-header strong').text(payload.data.title);
            toastElement.find('.toast-body').text(payload.data.body);
            toastElement.find('img').attr('src', payload.data.icon);

            var notificationSound = new Audio('/sounds/notification.mp3');
            notificationSound.play();

            // updateNotifications();

            setTimeout(function() {
                toastElement.css('display', 'block');
            }, 1000);
        });
    } else {
        console.error('This browser does not support Firebase Cloud Messaging.');
    }
</script>

