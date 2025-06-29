<style>
    .mainPopup {
        padding: 10px;
        border-radius: 1.6rem !important;
        position: absolute;
        transform: translate(-17%, -100%);
        width: 50%;
    }



    .mainPopup .driver-metadata {
        display: flex;
        align-items: center;
        border-bottom: 1px solid #D9D9D9;
        justify-content: space-between;
        padding: 0 8px;
        font-size: 11.2px;
    }

    .mainPopup .driverNamePhone {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .mainPopup  .driverNamePhone .driverImageWrapper {
        width: 20px;
        height: 20px;
    }

    .mainPopup  .driverNamePhone .driverImageWrapper img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
    }

    .mainPopup  .driverNamePhone .driverData .driverName {
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

    .mainPopup    .driverNamePhone .driverData .driverName:active {
        cursor: grabbing;
        /* Change cursor to grabbing while dragging */
    }

    .mainPopup  .driverNamePhone .driverData .driverName::-webkit-scrollbar {
        display: none;
        /* Hide scrollbar for Chrome, Safari, and Edge */
    }





    .mainPopup   .driverNamePhone .driverData .driverPhone {
        color: #949494;
        font-size: 9.6px;
        margin: 0;
        font-weight: 400;
    }

    .mainPopup .driverCar {
        width: 23px;
        height: 29.9px;
    }

    .mainPopup   .driverCar svg {
        width: 100%;
        height: 100%;
    }

    .mainPopup  .driverImageData {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 15px 10px 0;
        border-right: 1px solid #D9D9D9;
    }


    .mainPopup  .icons {
        display: flex;
        align-items: center;
    }

    .mainPopup  .icons .icon {
        padding: 4.8px 9.6px;
    }

    .mainPopup    .icons svg {
        width: 19.2px;
        height: 19.2px;
    }

    .mainPopup   .icons .icon:first-child {
        border-right: 1px solid #D9D9D9;
    }

    .mainPopup   .icons .icon:nth-child(2) {
        border-right: 1px solid #D9D9D9;
    }

    /* Timeline container */
    .mainPopup   .stepper-wrapper {
        cursor: grab;
        margin-top: auto;
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        width: 100%;
        /* Set the width of the container */
        overflow-x: auto;
        /* Enable horizontal scrolling */
        user-select: none;
        padding: 1rem;
        border-bottom: 1px solid #D9D9D9;
        scrollbar-width: none;
    }

    .mainPopup   .stepper-item {
        position: relative;
        display: flex;
        flex-direction: column;
        flex: 1;
        flex: 0 0 auto;
        /* Ensure each item does not stretch */
        width: 150px;
        transition: all 1s ease;

        @media (max-width: 768px) {
            font-size: 12px;
        }
    }

    .mainPopup   .stepper-wrapper::-webkit-scrollbar {
        display: none !important;

    }

    .mainPopup  .stepper-item::before {
        position: absolute;
        content: "";
        border-bottom: 2px solid #ccc;
        width: 50%;
        top: 10px;
        left: -50%;
        z-index: 2;
    }

    .mainPopup   .stepper-item::after {
        position: absolute;
        content: "";
        border-bottom: 2px solid #ccc;
        width: 50%;
        top: 10px;
        left: 0%;
        z-index: 2;
    }

    .mainPopup   .stepper-item .step-counter {
        position: relative;
        z-index: 5;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-bottom: 6px;
    }

    .mainPopup   .step-counter svg {
        width: 10px;
    }

    .mainPopup  .stepper-item.active {
        font-weight: bold;
    }

    .mainPopup   .stepper-item .step-counter {
        border: 1px solid #4bb543;
        background-color: #fff;
        width: 20px;
        height: 20px;
        padding: 2px;
    }

    .mainPopup   .stepper-item.completed::after {
        position: absolute;
        content: "";
        border-bottom: 2px solid #4bb543;
        width: 100%;
        top: 10px;
        left: 0;
        z-index: 3;
    }

    .mainPopup   .stepper-item:first-child::before {
        content: none;
    }

    .mainPopup   .stepper-item:last-child::after {
        content: none;
    }

    .mainPopup  .step-name {
        display: flex;
        flex-direction: column;
        font-size: 9.6px;
        color: #585858;

    }

    .mainPopup   .step-name p {
        margin: 0;
    }

    .mainPopup   .step-name p:nth-child(2) {
        font-size: 8px;
        color: #949494;
    }

    .mainPopup   .step-name p:nth-child(2) span:last-child {
        font-size: 8px;
        color: #4bb543;
    }


    /* Collapse Container */
    .mainPopup    .collapse-container {
        padding: 8px;
    }

    .mainPopup   .collapse-tab {
        padding: 8px 12.8px;
        border: 1px solid #D9D9D9;
        border-radius: .8rem;
        cursor: pointer;
        transition: all 0.2s ease;
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-decoration: none;
    }

    .mainPopup     .collapse-tab:hover {
        text-decoration: none;
    }

    .mainPopup   .collapseIcon {
        width: 19.2px;
        position: relative;
        top: -3px;
    }

    .mainPopup   .collapseIcon svg {
        width: 100%;
        height: 100%;
    }

    .mainPopup    .collapseNameIcon h4 {
        font-size: 12.8px;
        color: #3a3a3a;
        font-weight: 500;
        margin-bottom: 0;
    }

    .mainPopup    .collapseNameIcon {
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .mainPopup    .collapseBadge {
        font-size: 9.6px;
        color: #22ad2f;
        padding: .3rem 1.2rem;
        background-color: #e6f5e6;
        border-radius: .8rem;
    }

    .mainPopup   .editIcon {
        width: 16px;
        position: relative;
        top: -3px;

    }

    .mainPopup     .editIcon svg {
        width: 100%;
        height: 100%;
    }

    .mainPopup   .collapseArrowsBadges {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 8px;
    }

    .mainPopup   .collapse-tab:not(.collapsed) .downarrow svg {
        transform: rotate(180deg);
    }

    .mainPopup   .collapse-tab:not(.collapsed) {
        border-radius: 8px 8px 0 0;
        border-bottom: none;
    }

    .mainPopup  .collapse-container .card {
        border-radius: 0 0 8px 8px !important;
    }


    /* Card Style */
    .mainPopup    .driverDetails {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        padding: 8px 0;
        gap: 1rem;
    }

    .mainPopup   .driverDetails .shopName span,
    .mainPopup .shipmentId span {
        font-size: 8px;
        color: #949494;
    }

    .mainPopup   .driverDetails .shopName p,
    .mainPopup   .shipmentId p {
        font-size: 9.6px;
        color: #585858;
        font-weight: 400;
        margin: 0;
    }

    .mainPopup   .shipmentId p {
        color: #000;
        font-weight: 500;
        background-color: #f9f9f9;
        border-radius: .8rem;
        padding: .3rem .8rem;
    }

    .mainPopup   .driverDetails .shopImage {
        width: 28.8px;
        height: 28.8px;
        border: .5px solid #F2F2F2;
        text-align: center;
    }

    .mainPopup    .driverDetails .shopImage img {
        width: 80%;
        height: 80%;
        border-radius: 50%;
    }

    .mainPopup    .driverDetails .detailItem {
        display: flex;
        align-items: center;
        gap: 1rem;
        border-right: .5px solid #F2F2F2;
        padding: 0 8px;
    }

    .mainPopup  .shipmentId {
        width: fit-content;
        padding: 0 8px;
        margin: 1rem 0;

    }

    .mainPopup   .collapse-wrapper {
        max-height: 100%;
        overflow-y: auto;
    }

    .mainPopup    .collapse-wrapper::-webkit-scrollbar {
        display: none;
    }

    .mainPopup  .seeMoreBtn {
        width: 100%;
        border: none;
        background-color: #fff;
        padding: 8px 0;
        color: #f46624;
        font-size: 12.8px;
        font-weight: 600;
        cursor: pointer;
    }


    /* Drop Down Menu */

    .mainPopup  .dropdown-toggle::after{
    display: none !important;
}
.mainPopup  .dropdown-menu{
    margin: 0;
    padding: 0 !important;
    border-radius: 16px !important;
    min-width: 7rem;
    border: .1rem solid #F2F2F2;
    overflow: hidden;
    margin-top: .1rem;

}
.mainPopup  .dropdown-menu li a{
    cursor: pointer;
    list-style: none;
    padding: 14px;
    width: 100%;
    background-color: #fff;
    font-size: 12px;
}
.mainPopup .dropdown-menu li:hover a{
    color: #fff !important;
    background-color: #f46624 !important;
}



</style>
