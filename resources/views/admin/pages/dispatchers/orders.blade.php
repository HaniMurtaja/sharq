<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->

<style>
    .modal.show {
        opacity: 1 !important;
    }

    .mous-click-new {
        cursor: pointer;
    }
</style>

<style>
    .card {
        margin-bottom: 5px;
    }

    .card-header {
        padding: 5px 10px;
    }

    .card-body {
        padding: 5px 10px;
    }

    .btn-link {
        padding: 5;
    }

    #modal-container {
        transform: none !important;
        background-color: rgba(0, 0, 0, .1) !important;
        height: 100%;
    }

    .table-responsive {
        display: block !important;
        width: 100% !important;
    }

    /* CSS to hide scrollbar while allowing scrolling */
    .table-responsive {
        overflow: auto;
        /* Keep content scrollable */
        scrollbar-width: none;
        /* Firefox */
        -ms-overflow-style: none;
        /* IE 10+ */
    }

    .table-responsive::-webkit-scrollbar {
        display: none;
        /* Chrome, Safari, Edge */
    }
</style>



<style>
    .table td {
        white-space: nowrap;
        /* Prevents text from wrapping */
        overflow: hidden;
        /* Hides any overflow text */
        text-overflow: ellipsis;
        /* Adds an ellipsis (...) for overflow text */
        max-width: 500px;
        /* Adjust this width as needed */
    }

    /* Add padding and vertical alignment */
    .table td,
    .table th {
        padding: 8px;
        vertical-align: middle;
        /* Center-aligns content vertically */
    }

    /* Optional: Style for icons and links */
    .table a {
        color: #343a40;
        text-decoration: none;
    }

    .table a:hover {
        color: #f46624;
    }
</style>

<style>
    .table td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 400px;
    }

    .table td,
    .table th {
        padding: 8px;
        vertical-align: middle;
    }

    .table a {
        color: #343a40;
        text-decoration: none;
    }

    .table a:hover {
        color: #f46624;
    }

    #modal-container {
        transform: none !important;
        background-color: rgba(0, 0, 0, .1) !important;
        height: 100%;
    }
</style>



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

    .customcard {
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




<div>




    @include('admin.pages.dispatchers.OrderData')

</div>
