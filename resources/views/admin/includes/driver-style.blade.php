<style>

    .driverPopup {
        padding: 16px 19.2px;
        border-radius: 12.8px;
        width:600px !important;
    }


    .driverPopup .driver-metadata {
        display: flex;
        align-items: center;
        border-bottom: 1px solid #D9D9D9;
        justify-content: space-between;
        padding: 8px;
    }

    .driverPopup .driverNamePhone {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .driverPopup .driverNamePhone .driverImageWrapper {
        width: 32px;
        height: 32px;
    }

    .driverPopup .driverNamePhone .driverImageWrapper img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
    }

    .driverPopup .driverNamePhone .driverData .driverName {
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

    }

    .driverPopup .driverNamePhone .driverData .driverName:active {
        cursor: grabbing;
        /* Change cursor to grabbing while dragging */
    }

    .driverPopup .driverNamePhone .driverData .driverName::-webkit-scrollbar {
        display: none;
        /* Hide scrollbar for Chrome, Safari, and Edge */
    }





    .driverPopup .driverNamePhone .driverData .driverPhone {
        color: #949494;
        font-size: 9.6px;
        margin: 0;
        font-weight: 400;
    }

    .driverPopup .driverCar {
        width: 23px;
        height: 29.9px;
    }

    .driverPopup .driverCar svg {
        width: 100%;
        height: 100%;
    }

    .driverPopup .driverImageData {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 15px 10px 0;
        border-right: 1px solid #D9D9D9;
    }


    .driverPopup .driverTasks {
        display: flex;
        align-items: center;
    }

    .driverPopup .driverTasks span {
        padding: 6.4px 12.8px;
        font-size: 9.6px;
        background-color: #f9f9f9;
        color: #949494;
        border-radius: 9.6px;
    }

    .driverPopup .icons svg {
        width: 19.2px;
        height: 19.2px;
    }

    .driverPopup  .icons .icon:first-child {
        border-right: 1px solid #D9D9D9;
    }

    .driverPopup .icons .icon:nth-child(2) {
        border-right: 1px solid #D9D9D9;
    }



    /* Collapse Container */
    .driverPopup  .collapse-container {
        padding: 8px;
    }

    .driverPopup  .collapse-container .card-body {
        padding: 0 12px 6px 6px;
        border: none;
        border-top: 1px solid #D9D9D9;
        margin-top: .5rem;
    }

    .driverPopup .collapse-container .scroll-icon {
        width: 19.2px;
    }

    .driverPopup  .collapse-tab {
        /* transition: all 0.2s ease; */
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-decoration: none;
    }

    .driverPopup  .collapse-tab:hover {
        text-decoration: none;
    }

    .driverPopup  .collapseIcon {
        width: 19.2px;
        position: relative;
        top: -3px;
    }

    .driverPopup  .collapseIcon svg {
        width: 100%;
        height: 100%;
    }

    .driverPopup  .collapseNameIcon h4 {
        font-size: 12.8px;
        color: #3a3a3a;
        font-weight: 500;
        margin-bottom: 0;
    }

    .driverPopup  .collapseNameIcon {
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .driverPopup   .collapseBadge {
        font-size: 9.6px;
        color: #22ad2f;
        padding: .3rem 1.2rem;
        background-color: #e6f5e6;
        border-radius: .8rem;
    }

    .driverPopup  .editIcon {
        width: 16px;
        position: relative;
        top: -3px;

    }

    .driverPopup  .editIcon svg {
        width: 100%;
        height: 100%;
    }

    .driverPopup  .collapseArrowsBadges {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 8px;
    }

    .driverPopup  .collapse-tab:not(.collapsed) .downarrow svg {
        transform: rotate(180deg);
    }

    .driverPopup  .collapse-tab:not(.collapsed) {
        border-radius: 8px 8px 0 0;
        border-bottom: none;
    }

    .driverPopup  .collapse-container .card {
        border-radius: 0 0 8px 8px !important;
    }


    /* Card Style */
    .driverPopup  .driverDetails {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        padding: 8px 0;
        gap: 1rem;
    }

    .driverPopup   .driverDetails .shopName span,
    .driverPopup   .shipmentId span {
        font-size: 8px;
        color: #949494;
    }

    .driverPopup    .driverDetails .shopName p,
    .driverPopup    .shipmentId p {
        font-size: 9.6px;
        color: #585858
        font-weight: 400;
        margin: 0;
    }

    .driverPopup   .driverDetails .shopImage {
        width: 28.8px;
        height: 28.8px;
        border: .5px solid #F2F2F2;
        text-align: center;
    }

    .driverPopup    .driverDetails .shopImage img {
        width: 80%;
        height: 80%;
        border-radius: 50%;
    }

    .driverPopup    .driverDetails .detailItem {
        display: flex;
        align-items: center;
        gap: 1rem;
        border-right: .5px solid #F2F2F2;
        padding: 0 8px;
    }


    .driverPopup  .collapse-wrapper {
        max-height: 233px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .driverPopup   .collapse-wrapper::-webkit-scrollbar {
        display: none !important;
    }

    .driverPopup    .driverDetails .detailItem:last-child {
        border-right: 0;
    }

    .driverPopup   .collapse-container {
        display: flex;
        align-items: center;
        padding: 8px 12.8px;
        border: 1px solid #000;
        border-radius: .8rem;
        cursor: grab;
        gap: 1rem;

    }

    .driverPopup   .collapseContent {
        width: 100%;

    }

    .driverPopup   .bullet {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #000;
    }

    .driverPopup    .closeBtn {
        width: 26px;
        height: 26px;
        padding: 3px;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: rgba(0, 0, 0, .8);
        border-radius: 6px;
        border: none;
        position: absolute;
        top: -35px;
        right: 0px;
    }

    .driverPopup  .closeBtn svg {
        width: 17px;
        height: 17px;
        fill: #fff
    }

    .driverPopup  .closeBtn:hover {
        background-color: #f46624;
    }
</style>
