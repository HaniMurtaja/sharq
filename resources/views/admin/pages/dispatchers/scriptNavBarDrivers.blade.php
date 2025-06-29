<style>
    .collapseCustomBtn .orderCount {
        color: #1a1a1a;
        font-size: 11.6px;
        font-weight: 700;
    }

    .delayedCount {
        color: #ff4b36;
        font-size: 9px;
        font-weight: 600;
    }

    .customCard {
        padding: 9.4px 6.4px;
        margin: 4.6px 0;
        border-radius: .7rem;
        border: .7px solid #cecece;
        background-color: #f9f9f9;
        display: flex;
        justify-content: space-between;
        gap: .3rem;
        position: relative;
        align-items: center;
        line-height: normal;

    }

    .cardLeftSide {
        font-size: 9.6px;
        color: #585858;
        display: flex;
        align-items: center;
        gap: .5rem;
        font-weight: 600;
        /* width: 100%; */
    }

    .cardLeftSide input {
        position: absolute;
        top: 5px;
        /* left: 12px; */

    }

    .cardLeftSide span:last-child {
        font-size: 7px;
        width: 112px;
    }

    .cardLeftSide .cardImageWrapper {
        width: 40px;
        height: 40px;
    }

    .cardLeftSide .cardImageWrapper img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
    }

    .cardContent {
        display: flex;
        flex-direction: column;
        text-transform: capitalize;
        gap: .5rem;


    }

    .cardIdStatus {
        display: flex;
        align-items: center;
        gap: .5rem;

    }

    .cardContent .status.pending {
        background-color: orange;
        color: white;
    }

    .status.picked-up {
        background-color: blue;
        color: white;
    }

    .cardContent .status.approved {
        background-color: green;
        color: white;
    }

    .cardContent .status.rejected {
        background-color: red;
        color: white;
    }

    .cardContent .status.unknown {
        background-color: gray;
        color: white;
    }

    .cardIdStatus span,
    .cardContent .status {
        font-size: 9.2px;
        font-weight: 600;
        color: #000;
        letter-spacing: .5;


        padding: 3.2px 4.2px;

        overflow: hidden;
        margin: 0;
        text-align: center;
        line-height: normal;

    }

    .text-slide-wrapper {
        background-color: #e6f5e6;
        border-radius: .3rem;
        text-align: center;
        overflow: hidden;
        white-space: nowrap;
        width: 70px;
        position: relative
    }

    .text-slide {
        display: inline-block;
        position: relative
    }

    .text-slide-wrapper:hover .text-slide {
        animation: textSlide 3s ease-in-out forwards infinite;
    }

    .cardContent .status {
        font-size: 8px;
        color: #fff;
        background-color: #DF0655;
        border-radius: .3rem;
        padding: 3.2px 4.2px;
        margin: 0;
        text-align: center;
        font-weight: 600;
    }

    .cardTimeOperations {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
    }

    .cardTimeOperations p {
        font-size: 9.6px;
        color: #4bb543;
        margin: 0;
        font-weight: 600;

    }

    .cardRightSide {
        display: flex;

        flex-direction: column;
        gap: .7rem;

    }

    .driverCardImage {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: .5rem;

    }

    .driverCardImage img {
        width: 16px;
        height: 16px;
        border-radius: 50%;
    }

    #jobs-screen,
    #driversContainer {
        background-color: #fff;
        height: 83%;
        border-radius: 22.6px;
    }

    #driversContainer {
        background: transparent;
    }

    #jobs-screen .tab-content,
    #driversContainer .tab-content {
        max-height: 63vh;
        overflow: scroll;
        scrollbar-width: none;
        -ms-overflow-style: none;


    }

    #jobs-screen .tab-content::-webkit-scrollbar,
    #driversContainer .tab-content::-webkit-scrollbar {
        display: none;

    }

    #jobs-screen .tab-pane::-webkit-scrollbar,
    #driversContainer .tab-pane::-webkit-scrollbar {
        display: none;

    }

    #jobs-screen .nav-tabs .nav-link,
    #driversContainer .nav-tabs .nav-link {
        color: #333;
    }

    #jobs-screen .nav-tabs .nav-link.active,
    #driversContainer .nav-tabs .nav-link.active {
        color: #1a1a1a !important;

    }

    #jobs-screen .input-group,
    #driversContainer .input-group {
        position: relative;
    }

    #jobs-screen {
        display: none;
        border-radius: 3px;
    }

    #jobs-screen .jobs {
        padding: 19.2px 19.2px 0;

    }

    #jobs-screen .jobs h1 {
        font-size: 11.6px;
        color: #949494;
        font-weight: 600;
    }

    #jobs-screen .jobs p {
        font-size: 9.6px;
        color: #585858;
        font-weight: 600;
    }

    #jobsTabs .nav-link,
    #driversTabs .nav-link {
        font-size: 10.6px;
        color: #949494 !important;
        font-weight: 600;
        background: transparent;
        border: none;
    }

    #jobsTabs {
        height: 81%;
        overflow-y: scroll;
    }

    #jobsTabs,
    #driversTabs {
        background-color: #f9f9f9;
        justify-content: center;
        width: 90%;
        border-radius: 11px;
        margin: 16px auto 0;
    }

    #driversTabs {
        background: #fff;
        box-shadow: 1px 1px 1px #ccc, -1px 1px 1px #ccc;
        justify-content: space-around;
        align-items: center;

    }

    #jobs-screen .tab-pane,
    #driversContainer .tab-pane {
        width: 90%;
        margin: auto;
        background: transparent;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    #driversContainer .nav-link {
        padding: .3rem .5rem !important;
        margin: .2rem 0;
        width: max-content;
    }

    #driversContainer .nav-tabs .nav-item:has(.nav-link.active) {
        background-color: #f2f2f2;
        border-radius: 8px;

    }

    #driversContainer .nav-tabs .nav-item:first-child:has(.nav-link.active) .nav-link.active {
        color: #4bb543 !important;
        border-bottom: none !important;
    }

    #driversContainer .nav-tabs .nav-item:nth-child(2):has(.nav-link.active) .nav-link.active {
        color: #DF0655 !important;
        border-bottom: none !important;
    }

    #driversContainer .nav-tabs .nav-item:last-child:has(.nav-link.active) .nav-link.active {
        color: #585858 !important;
        border-bottom: none !important;
    }


    .cardRightSide .dropdown-toggle::after {
        display: none;
    }

    .cardRightSide .dropdown svg {
        width: 19.2px;
        height: 19.2px;
    }

    .cardRightSide .dropdown-menu {
        margin: 0;
        padding: 0 !important;
        border-radius: 16px !important;
        min-width: 7rem;
        border: .1rem solid #F2F2F2;
        overflow: hidden;
        margin-top: .1rem;

    }

    .cardRightSide .dropdown-menu li a {
        cursor: pointer;
        list-style: none;
        padding: 7px 14px;
        width: 100%;
        background-color: #fff;
        font-size: 9.6px;
    }

    .cardRightSide .dropdown-menu li:hover a {
        color: #fff !important;
        background-color: #f46624 !important;
    }

    #driversContainer .customCard {
        background-color: #fff !important;
        border-color: #fff !important;
        border: 1px solid #fff;
    }

    #driversContainer .customCard:hover {
        border: 1px solid #949494 !important;

    }

    #driversContainer .customCard img {
        object-fit: cover;
        object-position: top;
    }

    #driversContainer .customCard .driverPhone {
        font-size: 9.6px;
        color: #949494;
        font-weight: 600;
    }

    .text-slide-wrapper {
        overflow: hidden;
        white-space: nowrap;
        width: 65px;
        position: relative;
        font-weight: 600;
        font-size: 12.8px;
        background-color: #fff;
        color: #585858;
        text-align: start;
        margin-bottom: 3px;

    }

    #driversContainer .customCard .text-slide-wrapper {
        width: 160px !important;
    }

    .text-slide {
        display: inline-block;
        position: relative;
    }

    .text-slide-wrapper:hover .text-slide {
        animation: textSlide 3s ease-in-out forwards;

    }

    .customCard .messageIcon .tasksLeft {
        font-size: 9.6px;
        font-weight: 600;
        background-color: #f9f9f9;
        color: #585858;
        border-radius: 50%;
        padding: 2px 5px !important;
    }

    .lastOnline p:first-child {
        font-weight: 600;
        font-size: 8px;
        color: #949494;
        margin-bottom: 3px;
    }

    .lastOnline p:last-child {
        white-space: normal;
        text-align: center;
        font-size: 8px;
        font-weight: 600;
        color: #6c6c6c;
    }

    .greenBorder {
        border: 2px solid #4bb543;
    }

    .redBorder {
        border: 2px solid #DF0655;
    }

    .grayBorder {
        border: 2px solid #585858;
    }
</style>
<script>
    $(document).ready(function() {
        let get_driver_data_route = '{{ route('GetDriversData') }}';
        let driverCurrentRequest = null;
        let PAGE_NUMBER = 1;

        function fetchDriverData(type) {
            if (driverCurrentRequest) {
                driverCurrentRequest.abort();
            }

            driverCurrentRequest = $.ajax({
                url: get_driver_data_route,
                method: 'GET',
                data: {
                    page: PAGE_NUMBER,
                    type: type,
                    search: $('#driver_search').val()
                },
                success: function(response) {
                    // console.log("API Response:", response); // Debugging
                    // for (const [status, count] of Object.entries(response.operator_counts)) {
                    //     $(`#${status}_count`).text(count);
                    // }

                    // const statuses = ['available', 'busy', 'offline'];
                    // statuses.forEach(status => {
                    //     $(`#${status}`).empty(); // Clear previous data before appending
                    //     const drivers = response[status];

                    //     if (drivers && drivers.length > 0) {
                    //         drivers.forEach(driver => {
                    //             let borderClass = 'greenBorder';
                    //             if (status === 'busy') borderClass = 'redBorder';
                    //             if (status === 'offline') borderClass =
                    //             'grayBorder';

                    //             const driverCard = `
                    //             <div class="customCard">
                    //                 <div class="driverInfo d-flex gap-2 align-items-center">
                    //                     <div class="w-8 h-8 rounded-full">
                    //                         <img src="${driver.photo || '{{ asset('new/src/assets/images/user.jpg') }}'}"
                    //                             alt="Driver Image"
                    //                             class="rounded-full w-8 h-8 ${borderClass}"
                    //                             width="100" height="100%"
                    //                             onclick="openDriverPopup(${driver.lat}, ${driver.lng}, ${driver.id})"/>
                    //                     </div>
                    //                     <div>
                    //                         <div class="text-slide-wrapper">
                    //                             <p class="text-slide">${driver.full_name || 'No Name'}</p>
                    //                         </div>
                    //                         <p class="driverPhone m-0">${driver.phone || 'No Phone'}</p>
                    //                     </div>
                    //                 </div>
                    //             </div>`;
                    //             $(`#${status}`).append(driverCard);
                    //         });
                    //     }
                    // });

                    // PAGE_NUMBER++;
                },
                complete: function() {
                    driverCurrentRequest = null;
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }

        // ✅ Handle Tab Clicks Properly (Bootstrap 5)
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(event) {
            if (!$(this).hasClass('custom-tab-btn-for-rate')) {
                let selectedType = getActiveTabType();
                console.log("Tab changed, fetching data for type:", selectedType);
                PAGE_NUMBER = 1;
                fetchDriverData(selectedType);
            }
        });


        // ✅ Handle Search Input
        $("#driver_search").on("keyup change", function() {
            $('#available, #busy, #offline').empty();
            PAGE_NUMBER = 1;
            let activeType = getActiveTabType();
            fetchDriverData(activeType);
        });

        // ✅ Handle Scroll to Load More Data
        let scrollTimeout;
        $('.tab-pane-drivers').on('scroll', function() {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight -
                    5) {
                    console.log('Fetching more drivers...');
                    let activeType = getActiveTabType();
                    fetchDriverData(activeType);
                }
            }, 200);
        });

        // ✅ Detect Active Tab
        function getActiveTabType() {
            let activeTab = $('#driversTabs .nav-link.active').attr("id");
            console.log("Active Tab Detected:", activeTab); // Debugging

            switch (activeTab) {
                case "pending-tab":
                    return 1; // Available
                case "completed-tab":
                    return 2; // Busy
                case "cancelled-tab":
                    return 4; // Offline
                default:
                    return null;
            }
        }

        // ✅ Fetch initial data for the first active tab
        let defaultTab = getActiveTabType();
        fetchDriverData(defaultTab);
    });
</script>
