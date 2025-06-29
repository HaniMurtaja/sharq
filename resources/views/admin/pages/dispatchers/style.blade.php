<style>
    .orders-color {
        color: #343a40
    }
</style>
<style>
    body {
        background: #eee
    }


    html,
    body {
        height: 100%;
        margin: 0;
        padding: 0;
        overflow: hidden;
        /* Prevents scrollbars */
    }

    .full-height-container {
        height: 100vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .full-height-row {
        flex: 1;
        display: flex;
        overflow: hidden;
    }

    .full-height-column {
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
    }

    .full-height-card {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .full-height-card .card-header {
        flex-shrink: 0;
    }

    .full-height-card .tab-content {
        flex-grow: 1;
        overflow-y: auto;
        overflow-x: hidden;
    }


    #regForm {
        background-color: #ffffff;
        margin: 0px auto;
        font-family: Raleway;
        padding: 40px;
        border-radius: 10px
    }

    h1 {
        text-align: center
    }

    input {
        padding: 10px;
        width: 100%;
        font-size: 17px;
        font-family: Raleway;
        border: 1px solid #aaaaaa
    }

    input.invalid {
        background-color: #ffdddd
    }

    .tab {
        display: none
    }

    button {
        background-color: #4CAF50;
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        font-size: 17px;
        font-family: Raleway;
        cursor: pointer
    }

    button:hover {
        opacity: 0.8
    }

    #prevBtn {
        background-color: #bbbbbb
    }

    .step {
        height: 15px;
        width: 15px;
        margin: 0 2px;
        background-color: #bbbbbb;
        border: none;
        border-radius: 50%;
        display: inline-block;
        opacity: 0.5
    }

    .step.active {
        opacity: 1
    }

    .step.finish {
        background-color: #4CAF50
    }

    .all-steps {
        text-align: center;
        margin-top: 30px;
        margin-bottom: 30px
    }

    .thanks-message {
        display: none
    }
</style>
<style>
    .new-btn {
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 10%;
        padding: 5px 10px;
        font-size: 16px;
        cursor: pointer;
    }

    .new-btn:hover {
        background-color: darkblue;
    }


    .modal-dialog {
        max-width: 80%;
        max-height: 90%;
        width: 600px;
        /* Adjust as needed */
        height: 600px;
        /* Adjust as needed */
    }

    .modal-content {
        height: 100%;
    }

    .modal-body {
        overflow-y: auto;
        height: calc(100% - 100px);
    }
</style>

<style>
    body {
        background: #eee;
    }

    #regForm {
        background-color: #ffffff;
        margin: 0px auto;
        font-family: Raleway;
        padding: 40px;
        border-radius: 10px;
    }

    h1 {
        text-align: center;
    }

    input {
        padding: 10px;
        width: 100%;
        font-size: 17px;
        font-family: Raleway;
        border: 1px solid #aaaaaa;
    }

    input.invalid {
        background-color: #ffdddd;
    }

    .nav-pills .nav-link.active {
        color: #343a40 !important;
        background-color: #ffdddd !important
    }

    .tab {
        display: none;
    }

    button {
        background-color: #4CAF50;
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        font-size: 17px;
        font-family: Raleway;
        cursor: pointer;
    }

    button:hover {
        opacity: 0.8;
    }



    #prevBtn {
        background-color: #bbbbbb;
    }

    .step {
        height: 15px;
        width: 15px;
        margin: 0 2px;
        background-color: #bbbbbb;
        border: none;
        border-radius: 50%;
        display: inline-block;
        opacity: 0.5;
    }

    .step.active {
        opacity: 1;
    }

    .step.finish {
        background-color: #4CAF50;
    }

    .all-steps {
        text-align: center;
        margin-top: 0px;
        margin-bottom: 30px;
    }

    .textarea {
        width: 470px;
        height: 70px;
    }

    .thanks-message {
        display: none;
    }

    .new-btn {
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 10%;
        padding: 5px 10px;
        font-size: 16px;
        cursor: pointer;
    }

    .new-btn:hover {
        background-color: darkblue;
    }

    .txtarea {
        width: 227px;
        height: 70px;
    }

    .full-height-container {
        height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .full-height-row {
        flex: 1;
        display: flex;
    }

    .full-height-column {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .full-height-card {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .full-height-card .card-header {
        flex-shrink: 0;
    }

    .full-height-card .tab-content {
        flex-grow: 1;
        overflow-y: auto;
    }
</style>