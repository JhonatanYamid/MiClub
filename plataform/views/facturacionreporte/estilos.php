<style type = "text/css"> 
	@media print { 
		body {-webkit-print-color-adjust: exact;} 
	}

	 @page {
		size: letter landscape;
		margin: 0;
	}

	.marginFiltro {
		vertical-align: middle;
		margin: 5px 15px;
	}

	.marginElem {
		vertical-align: middle;
		margin-right: 5px;
	}

	.tallest {
		vertical-align: bottom;
		padding-top: 7px;
	}

	.tallest2 {
		vertical-align: bottom;
		padding: 10px;
	}

	.tablaPagos{
		width:70%;
		margin: 12px;
	}

	.tablaPagos input, .tablaPagos select{
		width:100%;
	}

	.tablaTotales{
		width:25%;
		font-weight: 250;
		font-size: 14px;
		margin: 12px;
	}
	
	.button_style{   
		background-color: Transparent !important;
		border: none;            
		cursor:hand;    
	}

	.modal {
		text-align: center;
		padding: 0 !important;
	}

	.modal:before {
		content: '';
		display: inline-block;
		height: 100%;
		vertical-align: middle;
		margin-right: -4px; /* Adjusts for spacing */
	}

	.modal-dialog {
		display: inline-block;
		text-align: left;
		vertical-align: middle;

		width:inherit;
	
		margin-left: auto;
		pointer-events: all;
	}

	.container {
		min-width: 1000px;
		margin: 0 auto;

		font-family: 'Source Sans Pro', sans-serif;
		font-weight: 250;
		font-size: 11px;
		color: #777777 !important;
	}

	.clearfix:after {
		content: "";
		display: table;
		clear: both;
	}

	.left {
		float: left !important;
	}

	.right {
		float: right !important;
	}

	.helper {
		height: 100%;
	}

	.headerFac {
		height: 80px;
		margin-top: 20px;
		margin-bottom: 30px;
	}

	.headerFac figure {
		float: left;
		margin: 0 5px 0 0;
		padding: 0;
	}

	.headerFac figure img {
		height: 80px;
	}

	.company-info {
		float: left;
		line-height: 1.7em;
	}

	.headerFac .company-info .title {
		color: <?= $info['Color1'] ?> !important;
		font-weight: 600;
		font-size: 2em;
		padding: 0 !important;
		margin: 0 !important;
	}

	.line {
		display: inline-block;
		height: 9px;
		margin: 0 4px;
		border-left: 1px solid <?= $info['Color2'] ?>;
	}

	.company-info .seller {
		margin-top: 60px;
		padding: 0 5px;
		color: <?= $info['Color1'] ?> !important; 
	}

	.company-contact {
		float: right;
		height: 60px;
		text-align: right;
	}

	.headerFac .company-contact .title {
		color: <?= $info['Color1'] ?> !important;
		font-weight: 400;
		font-size: 1.5em;
		text-transform: uppercase;
		padding: 0 !important;
		margin: 0 !important;
	}

	.headerFac .company-contact span {
		display: inline-block;
		vertical-align: middle;
	}

	.headerFac .company-contact i {
		color: <?= $info['Color2']; ?> !important;
		font-size: 14px;
	}

	.details {
		background-color: <?= $info['Color1'] ?> !important;
		color: #ffffff !important;
		min-width: 300px;
		margin-bottom: 30px;
		padding: 5px 20px; 
		text-align: center;
	}

	.client {
		width: 50%;
		line-height: 16px;
		text-align: left;
		font-weight: 300;
		font-size: 12px;
	}

	.client .titleC {
		font-weight: 400;
		font-size: 14px;
		margin-top: 5px;
	}

	.name {
		font-weight: 600;
		font-size: 14px;
	}

	.date {
		font-weight: 400;
		font-size: 14px;
		<? 
			if($tipo == 1) echo "padding-top: 10px;"
		?>
	}

	.data {
		width: 50%;
		text-align: right;
		padding-top: 10px;
	}

	.section .details .title {
		font-size: 2.5em;
		font-weight: 400;
	}

	.table-wrapper {
		position: relative;
		overflow: hidden;
	}

	.section table {
		width: 100%;
		margin-bottom: -8px;
		table-layout: fixed;
		border-collapse: separate;
		border-spacing: 4px 8px;
	}

	.desc {
		width: 27%;
	}

	.qty, .unit, .total {
		width: 10%;
	}

	.section table tbody.head {
		vertical-align: middle;
		border-color: inherit;
	}

	.section table tbody.head th {
		text-align: center;
		color: white !important;
		font-weight: 600;
		text-transform: uppercase;
	}
	.section table tbody.head th div {
		display: inline-block;
		padding: 7px 0;
		font-weight: 400;
		font-size: 12px;
		width: 100%;
		background: #969393 !important;
	}

	.section table tbody.body td {
		padding: 10px 3px;
		background: #F3F3F3 !important;
		text-align: center;
		font-weight: 300;
		font-size: 11.5px;
		color: #777777 !important;
	}

	.section table tbody.body .no {
		width: 3%;
		padding: 0px;
		background-color: <?= $info['Color1'] ?> !important;
		color: #ffffff !important;
		font-size: 13px;
		font-weight: 300;
		line-height: 50px;
	}
	.section table tbody.body .desc {
		text-align: left;
		white-space: initial;
		overflow: hidden;
	}

	.section table tbody.body .total {
		color: <?= $info['Color1'] ?> !important; 
		font-weight: 600;
	}

	.payments {
		float: left;
		width: 55%;
		line-height: 1.7em;
		margin-top: 30px;
	}

	.payments fieldset{
		border:2px solid <?=  $info['Color1']; ?>;
		width: 100%;
		display: block;
		padding: 0 0.75em;
	}

	.payments legend{
		background-color: #969393 !important;
		color: white !important;
		width: 15%;
		font-weight: 600;
		font-size: 1.16666666666667em;
		text-align: center;
		margin-bottom: 0 !important;
	}

	table.payments-list {
		border-collapse: separate;
		border-spacing: 0px 5px;
		width: 100%;
		margin-bottom: 5px;
		margin-top: 5px;
		font-weight: 400;
		font-size: 12px;
		color: #777777 !important;
	}

	.payments-list tbody td {
		padding: 5px;
		vertical-align: middle;
		background: #F3F3F3 !important;
		height: auto;
		white-space: initial;
	}

	.number{
		border-right: 1px solid <?= $info['Color2']?> !important;
		width: 5%;
		text-align: center;
	}
	.method{
		border-right: 1px solid <?= $info['Color2']?> !important;
		width: 35%;
		text-align: left;
	}

	.payment{
		border-right: 1px solid <?= $info['Color2']?> !important;
		width: 15%;
		text-align: center;
	}

	.note{
		width: 45%;
		text-align: left;
	}

	.totals{
		float: right;
		width: 30%;
		overflow: hidden;
		display: block;
		margin-top: 30px;
	}

	table.grand-total {
		border-collapse: collapse;
		border-spacing: 0px 0px;
		margin-bottom: 40px;
		width: 100%;
		
	}
	table.grand-total tbody td {
		background-color: <?= $info['Color1'] ?> !important;
		color: #ffffff !important; 
		text-align: right;
		margin: 0 !important;
		padding: 5px !important;
		font-weight: 300;
		font-size: 12px;
	}

	table.grand-total tbody td.grand-total {
		font-size: 13 !important;
		font-weight: 600 !important;
		background-color: <?= $info['Color2'] ?> !important;
	}
	table.grand-total tbody td.transparent {
		background-color: transparent !important;
	}

	.empty {
		width: 40%;
	}

	.value-name {
		padding-right: 0;
	}
	
	.value {
		padding-right: 10px !important;
	}

	.footerFac {
		width: 100%;
		/* margin-top: 50px; */
		padding: 0 5px;
	}
	.footerFac .end {
		padding-top: 10px;
		border-top: 2px solid <?= $info['Color1'] ?>;
		text-align: center;
	}

</style>