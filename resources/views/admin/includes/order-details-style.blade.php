<style>


    .orderDetailsModal .modal-content {
        padding: 25.6px !important;
        border-radius: 12.8px;
    }
    .orderDetailsModal:not(.show){
        z-index: -1;
    }

    .orderDetailsModal .modal-xl {
        width: 891.2px;
    }
    .orderDetailsModal .modal-header{
        border: none;
        padding: 0;
    }
    .orderDetailsModal .modal-body{
        padding-right: 0;
        padding-left: 0;
    }

    .orderDetailsModal .head h4 {
        font-size: 16px;
        font-weight: 700;
        line-height: 18.4px;
        margin: 0;
    }

    .orderDetailsModal .head p {
        font-size: 12.8px;
        line-height: 14.71px;
        margin: 0;
        padding-top: 8px;
    }

    /* .orderDetailsModal .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border: none;
        padding: 0;
        padding-bottom: 25.6px;
    } */

    .orderDetailsModal  .modal-header button {
        background-color: transparent;
        border: none;
        display: flex;
    }

    .orderDetailsModal  .modal-header button svg {
        width: 19.2px;
        height: 19.2px;
    }

    /* Map */

    .orderDetailsModal  .map {
        width: 100%;
        height: 176px;
        border-radius: 9.6px;
        background-color: #ccc;
    }




    /* Table Styles */

    .orderDetailsModal  table {
        border-collapse: separate;
        border-spacing: 0;
        min-width: 350px;

    }



    .orderDetailsModal    table tr th {
        /* border-top: 1px solid #bbb !important; */
        text-align: left;
        font-size: 11.2px;
        color: #b4b4b4;
        padding: 12.8px 8px;
        text-align: center;
        background-color: #fff;
    }

    .orderDetailsModal  table tr td {
        text-align: center;
        padding: 8px;
    }


    .orderDetailsModal  .driverImageData {
        display: flex;
        align-items: center;
        gap: 10px;

    }

    .orderDetailsModal  .driverData .driverPhone {
        color: #949494;
        font-size: 9.6px;
        margin: 0;
        font-weight: 400;
        text-align: left;
    }

    .orderDetailsModal   .driverImageWrapper {
        width: 32px;
        height: 32px;
    }

    .orderDetailsModal  .driverImageWrapper img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
    }

    .orderDetailsModal  .driverData .driverName {
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

    .orderDetailsModal  .driverData .driverName:active {
        cursor: grabbing;
        /* Change cursor to grabbing while dragging */
    }

    .orderDetailsModal  .driverData .driverName::-webkit-scrollbar {
        display: none;
        /* Hide scrollbar for Chrome, Safari, and Edge */
    }

    .orderDetailsModal  .destinationContainer {
        display: flex !important;
        align-items: center;
        gap: .5rem;
        padding: 12px;
        border-top: none !important;
    }

    .orderDetailsModal  .destinationContainer svg {
        width: 24px;
        height: 24px;
    }

    .orderDetailsModal  .destination h5 {
        font-size: 9.6px;
        font-weight: 700;
        margin: 0;
    }

    .orderDetailsModal  .destination p {
        font-size: 8px;

    }

    .orderDetailsModal .destination {
        position: relative;
        top: 8px;
        text-align: left;
    }

    .orderDetailsModal .assignBtn {
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

    .orderDetailsModal .scrollable-table {
        max-height: 250px;
        /* Adjust the height as needed */
        overflow-y: auto;
        border-radius: 6.4px;
        border: 1px solid #e2e5e9;
        margin-top: 1.5rem;
        -ms-overflow-style: none;
        scrollbar-width: none;

    }

    .orderDetailsModal table {
        width: 100%;
        /* Ensure the table takes full width */
        border-collapse: collapse;
        /* Prevent spacing between cells */
    }


    .orderDetailsModal   th {
        position: sticky;
        /* Keep the header fixed when scrolling */
        top: -2px;
        background-color: #f8f9fa;
        z-index: 2;
        /* Ensure the header stays above the content */
    }

    .orderDetailsModal  .scrollable-table::-webkit-scrollbar {
        display: none;
    }



/* Classes For Second modal */

.orderDetailsModal .driverIdentity{
  display: flex;
  justify-content: space-between;
  align-items: center;
  border: 1px solid #c8c8c8;
  padding: 8px;
  border-radius: 9.6px;
}

.orderDetailsModal  .changeDriverBtn{
  font-size: 11.2px;
  color: #585858;
  padding: 8px 28.8px;
  border-radius: 9.6px;
  border: 1px solid #c8c8c8;
  background-color: transparent;
}

.orderDetailsModal .driverOrdersDetails h3{
  margin: 25.6px 0 9.6px;
  font-weight: 600;
}
.orderDetailsModal .driverOrdersDetails p{
  color: #949494;
  font-size: 12.8px;
  font-weight: 400;
  margin: 0;
}
.orderDetailsModal .collapse-container{
  padding: 8px;
}
.orderDetailsModal .collapse-container .card-body{
  padding: 0 12px 6px 6px;
  border: none;
  border-top: 1px solid #D9D9D9;
  margin-top: .5rem;
}


.orderDetailsModal .collapse-tab{
  transition: all 0.2s ease;
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  text-decoration: none;
}
.orderDetailsModal .collapse-tab:hover{
  text-decoration: none;
}
.orderDetailsModal .collapseIcon{
    display: flex;
  justify-content: center;
  align-items: center;
    font-size: 9.6px;
    font-weight: 700;

    padding:16px;
    border-radius: 25%;
    color: #fff;
    width: 12.8px;
    height: 12.8px;
    position: relative;
    gap: 1.6px;
    background-color: #000;
    border: 1px solid #000;

    box-shadow: 0 0 0 2px #f3f5f6 inset;
    box-sizing: border-box;
}

.orderDetailsModal .collapseIcon p{
 margin: 0;
}
.orderDetailsModal .collapseNameIcon h4{
  font-size: 12.8px;
  color: #3a3a3a;
  font-weight: 500;
  margin-bottom: 0;
}

.orderDetailsModal .collapseNameIcon{
  display: flex;
  align-items: center;
  gap: .5rem;
}
.orderDetailsModal .collapseBadge{
  font-size:9.6px;
  color: #22ad2f;
  padding: .3rem 1.2rem;
  background-color: #e6f5e6;
  border-radius: .8rem;
}
.orderDetailsModal .editIcon{
  width:16px;
  position: relative;
  top: -3px;

}
.orderDetailsModal .editIcon svg{
  width: 100%;
  height: 100%;
}
.orderDetailsModal .collapseArrowsBadges{
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 8px;
}
.orderDetailsModal .collapse-tab:not(.collapsed) .downarrow svg{
  transform: rotate(180deg);
}
.orderDetailsModal .collapse-tab:not(.collapsed){
  border-radius: 8px 8px 0 0;
  border-bottom: none;
}
.orderDetailsModal .collapse-container .card{
  border-radius: 0 0 8px 8px !important;
}


/* Card Style */
.orderDetailsModal .driverDetails{
display: grid;
grid-template-columns: repeat(5, 1fr);
padding: 8px 0;
gap: 1rem;
}
.orderDetailsModal .driverDetails .shopName span, .shipmentId span{
font-size: 8px;
color: #949494;
}
.orderDetailsModal .driverDetails .shopName p , .shipmentId p{
font-size: 9.6px;
color: #585858;
font-weight: 400;
margin: 0;
}

.orderDetailsModal .driverDetails .shopImage{
width: 28.8px;
height: 28.8px;
border: .5px solid #F2F2F2;
text-align: center;
}
.orderDetailsModal .driverDetails .shopImage img{
width: 80%;
height: 80%;
border-radius: 50%;
}
.orderDetailsModal .driverDetails .detailItem{
display: flex;
align-items: center;
gap: 1rem;
border-right: .5px solid #F2F2F2;
padding: 0 8px;
}


.orderDetailsModal .collapse-wrapper{
max-height: 233px;
  overflow-y: scroll !important;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.orderDetailsModal .collapse-wrapper::-webkit-scrollbar {
display: none !important;
}
.orderDetailsModal .driverDetails .detailItem:last-child{
  border-right: 0;
}

.orderDetailsModal .collapse-container{
  display: flex;
  align-items: center;
  flex-direction: column;
  padding: 8px 12.8px;
  border: 1px solid #c8c8c8;
  border-radius: .8rem;
  cursor: grab;
  gap: 1rem;

}
.orderDetailsModal .collapseContent{
  width: 100%;

}
.orderDetailsModal .bullet{
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: #000;
}

.orderDetailsModal .driverOrdersDetailsContent p:first-child{
  font-size: 11.2px;
  color: #585858;
  margin: 0;
}
.orderDetailsModal .driverOrdersDetailsContent p{
  font-size: 9.6px;
  color: #949494;
  margin: 0;
}
.orderDetailsModal .driverOrdersDetailsContent{
  margin-left: .5rem;
}

.orderDetailsModal .ordersDetailsActionBtn{
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 1rem;
  margin: 25.6px 0 0;
}

.orderDetailsModal .changeDriverBtn.assignBtn{
  background-color: orangered;
  color: #fff;
  border: 1px solid orangered;
  padding: 8px 32.8px;

}
</style>
