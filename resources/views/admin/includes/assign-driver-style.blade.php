<style>


    .assignDriver {
        padding: 25.6px;
        border-radius: 12.8px;
    }
    .assignDriverModal:not(.show){
        z-index: -1;
    }

    .assignDriver .modal-xl {
        width: 891.2px;
    }

    .assignDriver .head h4 {
        font-size: 16px;
        font-weight: 700;
        line-height: 18.4px;
        margin: 0;
    }

    .assignDriver .head p {
        font-size: 12.8px;
        line-height: 14.71px;
        margin: 0;
        padding-top: 8px;
    }

    .assignDriver .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border: none;
        padding: 0;
        padding-bottom: 25.6px;
    }

    .assignDriver  .modal-header button {
        background-color: transparent;
        border: none;
        display: flex;
    }

    .assignDriver  .modal-header button svg {
        width: 19.2px;
        height: 19.2px;
    }

    /* Map */

    .assignDriver  .map {
        width: 100%;
        height: 176px;
        border-radius: 9.6px;
        background-color: #ccc;
    }




    /* Table Styles */

    .assignDriver  table {
        border-collapse: separate;
        border-spacing: 0;
        min-width: 350px;

    }



    .assignDriver    table tr th {
        /* border-top: 1px solid #bbb !important; */
        text-align: left;
        font-size: 11.2px;
        color: #b4b4b4;
        padding: 12.8px 8px;
        text-align: center;
        background-color: #fff;
    }

    .assignDriver  table tr td {
        text-align: center;
        padding: 8px;
    }


    .assignDriver  .driverImageData {
        display: flex;
        align-items: center;
        gap: 10px;

    }

    .assignDriver  .driverData .driverPhone {
        color: #949494;
        font-size: 9.6px;
        margin: 0;
        font-weight: 400;
        text-align: left;
    }

    .assignDriver   .driverImageWrapper {
        width: 32px;
        height: 32px;
    }

    .assignDriver  .driverImageWrapper img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
    }

    .assignDriver  .driverData .driverName {
        color: #585858;
        font-size: 12.8px;
        margin: 0;
        font-weight: 400;
        width: 120px;
        white-space: nowrap;
        overflow-x: auto;
        overflow-y: hidden;
        -ms-overflow-style: none;
        scrollbar-width: none;
        cursor: grab;
        user-select: none;
        text-align: left;

    }

    .assignDriver  .driverData .driverName:active {
        cursor: grabbing;
        /* Change cursor to grabbing while dragging */
    }

    .assignDriver  .driverData .driverName::-webkit-scrollbar {
        display: none;
        /* Hide scrollbar for Chrome, Safari, and Edge */
    }

    .assignDriver  .destinationContainer {
        display: flex !important;
        align-items: center;
        gap: .5rem;
        padding: 12px;
        border-top: none !important;
    }

    .assignDriver  .destinationContainer svg {
        width: 24px;
        height: 24px;
    }

    .assignDriver  .destination h5 {
        font-size: 9.6px;
        font-weight: 700;
        margin: 0;
    }

    .assignDriver  .destination p {
        font-size: 8px;

    }

    .assignDriver .destination {
        position: relative;
        top: 8px;
        text-align: left;
    }

    .assignDriver .assignBtn {
        background-color: orangered;
        color: #fff;
        border-radius: 9.6px;
        font-size: 12.2px;
        display: flex;
        justify-content: center;
        align-items: center;
        border: none;
        padding: 8.2px 20.8px;
        font-weight: 700;
    }

    .assignDriver .scrollable-table {
        max-height: 250px;
        /* Adjust the height as needed */
        overflow-y: auto;
        border-radius: 6.4px;
        border: 1px solid #e2e5e9;
        margin-top: 1.5rem;
        -ms-overflow-style: none;
        scrollbar-width: none;

    }

    .assignDriver table {
        width: 100%;
        /* Ensure the table takes full width */
        border-collapse: collapse;
        /* Prevent spacing between cells */
    }


    .assignDriver   th {
        position: sticky;
        /* Keep the header fixed when scrolling */
        top: -2px;
        background-color: #f8f9fa;
        z-index: 2;
        /* Ensure the header stays above the content */
    }

    .assignDriver  .scrollable-table::-webkit-scrollbar {
        display: none;
    }
    .assignDriver table tr td{
        cursor: pointer !important;
    }




</style>
