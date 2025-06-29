<style>
    .modal-content {
        padding: 10px;
        border-radius: 1.6rem;
    }


    .driver-metadata {
        display: flex;
        align-items: center;
        border-bottom: 1px solid #D9D9D9;
        justify-content: space-between;
        padding: 8px;
    }

    .driverNamePhone {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .driverNamePhone .driverImageWrapper {
        width: 32px;
        height: 32px;
    }

    .driverNamePhone .driverImageWrapper img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
    }

    .driverNamePhone .driverData .driverName {
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

    .driverNamePhone .driverData .driverName:active {
        cursor: grabbing;
        /* Change cursor to grabbing while dragging */
    }

    .driverNamePhone .driverData .driverName::-webkit-scrollbar {
        display: none;
        /* Hide scrollbar for Chrome, Safari, and Edge */
    }





    .driverNamePhone .driverData .driverPhone {
        color: #949494;
        font-size: 9.6px;
        margin: 0;
        font-weight: 400;
    }

    .driverCar {
        width: 23px;
        height: 29.9px;
    }

    .driverCar svg {
        width: 100%;
        height: 100%;
    }

    .driverImageData {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 15px 10px 0;
        border-right: 1px solid #D9D9D9;
    }


    .icons {
        display: flex;
        align-items: center;
    }

    .icons .icon {
        padding: 4.8px 9.6px;
    }

    .icons svg {
        width: 19.2px;
        height: 19.2px;
    }

    .icons .icon:first-child {
        border-right: 1px solid #D9D9D9;
    }

    .icons .icon:nth-child(2) {
        border-right: 1px solid #D9D9D9;
    }

    /* Timeline container */
    .stepper-wrapper {
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
    }

    .stepper-item {
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

    .stepper-wrapper::-webkit-scrollbar {
        display: none;
        /* For Webkit browsers like Chrome, Safari, and Edge */
    }

    .stepper-item::before {
        position: absolute;
        content: "";
        border-bottom: 2px solid #ccc;
        width: 50%;
        top: 10px;
        left: -50%;
        z-index: 2;
    }

    .stepper-item::after {
        position: absolute;
        content: "";
        border-bottom: 2px solid #ccc;
        width: 50%;
        top: 10px;
        left: 0%;
        z-index: 2;
    }

    .stepper-item .step-counter {
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

    .step-counter svg {
        width: 10px;
    }

    .stepper-item.active {
        font-weight: bold;
    }

    .stepper-item .step-counter {
        border: 1px solid #4bb543;
        background-color: #fff;
        width: 20px;
        height: 20px;
        padding: 2px;
    }

    .stepper-item.completed::after {
        position: absolute;
        content: "";
        border-bottom: 2px solid #4bb543;
        width: 100%;
        top: 10px;
        left: 0;
        z-index: 3;
    }

    .stepper-item:first-child::before {
        content: none;
    }

    .stepper-item:last-child::after {
        content: none;
    }

    .step-name {
        display: flex;
        flex-direction: column;
        font-size: 9.6px;
        color: #585858;

    }

    .step-name p {
        margin: 0;
    }

    .step-name p:nth-child(2) {
        font-size: 8px;
        color: #949494;
    }

    .step-name p:nth-child(2) span:last-child {
        font-size: 8px;
        color: #4bb543;
    }


    /* Collapse Container */
    .collapse-container {
        padding: 8px;
    }

    .collapse-tab {
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

    .collapse-tab:hover {
        text-decoration: none;
    }

    .collapseIcon {
        width: 19.2px;
        position: relative;
        top: -3px;
    }

    .collapseIcon svg {
        width: 100%;
        height: 100%;
    }

    .collapseNameIcon h4 {
        font-size: 12.8px;
        color: #3a3a3a;
        font-weight: 500;
        margin-bottom: 0;
    }

    .collapseNameIcon {
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .collapseBadge {
        font-size: 9.6px;
        color: #22ad2f;
        padding: .3rem 1.2rem;
        background-color: #e6f5e6;
        border-radius: .8rem;
    }

    .editIcon {
        width: 16px;
        position: relative;
        top: -3px;

    }

    .editIcon svg {
        width: 100%;
        height: 100%;
    }

    .collapseArrowsBadges {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 8px;
    }

    .collapse-tab:not(.collapsed) .downarrow svg {
        transform: rotate(180deg);
    }

    .collapse-tab:not(.collapsed) {
        border-radius: 8px 8px 0 0;
        border-bottom: none;
    }

    .collapse-container .card {
        border-radius: 0 0 8px 8px !important;
    }


    /* Card Style */
    .driverDetails {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        padding: 8px 0;
        gap: 1rem;
    }

    .driverDetails .shopName span,
    .shipmentId span {
        font-size: 8px;
        color: #949494;
    }

    .driverDetails .shopName p,
    .shipmentId p {
        font-size: 9.6px;
        color: #585858;
        font-weight: 400;
        margin: 0;
    }

    .shipmentId p {
        color: #000;
        font-weight: 500;
        background-color: #f9f9f9;
        border-radius: .8rem;
        padding: .3rem .8rem;
    }

    .driverDetails .shopImage {
        width: 28.8px;
        height: 28.8px;
        border: .5px solid #F2F2F2;
        text-align: center;
    }

    .driverDetails .shopImage img {
        width: 80%;
        height: 80%;
        border-radius: 50%;
    }

    .driverDetails .detailItem {
        display: flex;
        align-items: center;
        gap: 1rem;
        border-right: .5px solid #F2F2F2;
        padding: 0 8px;
    }

    .shipmentId {
        width: fit-content;
        padding: 0 8px;
        margin: 1rem 0;

    }

    .collapse-wrapper {
        max-height: 100%;
        overflow-y: auto;
    }

    .collapse-wrapper::-webkit-scrollbar {
        display: none;
    }

    .seeMoreBtn {
        width: 100%;
        border: none;
        background-color: #fff;
        padding: 1rem 0;
        color: #f46624;
        font-size: 12.8px;
        font-weight: 600;
        cursor: pointer;
    }
</style>
