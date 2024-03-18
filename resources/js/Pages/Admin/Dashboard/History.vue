<script setup>
defineProps({
	orders: Array,
});
</script>
<template>
	<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
		<div class="card lg:col-span-2 card-border">
			<div>
				<div class="flex card-body items-center justify-between mb-6">
					<h4>Latest Orders</h4>
					<button
						class="button bg-white border border-gray-300 dark:bg-gray-700 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 active:bg-gray-100 dark:active:bg-gray-500 dark:active:border-gray-500 text-gray-600 dark:text-gray-100 radius-round h-9 px-3 py-2 text-sm"
					>
						View Orders
					</button>
				</div>
				<div class="overflow-x-auto">
					<table
						class="table-default table-hover"
						role="table"
					>
						<thead class="">
							<tr
								class=""
								role="row"
							>
								<th
									class=""
									colspan="1"
									role="columnheader"
								>
									Order
								</th>
								<th
									class=""
									colspan="1"
									role="columnheader"
								>
									Status
								</th>
								<th
									class=""
									colspan="1"
									role="columnheader"
								>
									Date
								</th>
								<th
									class=""
									colspan="1"
									role="columnheader"
								>
									Customer
								</th>
								<th
									class=""
									colspan="1"
									role="columnheader"
								>
									Total
								</th>
							</tr>
						</thead>
						<tbody
							class=""
							role="rowgroup"
						>
							<tr
								class=""
								role="row"
							>
								<td
									class=""
									role="cell"
								>
									<span
										class="cursor-pointer select-none font-semibold hover:text-blue-600"
										>#95954</span
									>
								</td>
								<td
									class=""
									role="cell"
								>
									<div class="flex items-center">
										<span class="badge-dot bg-emerald-500"></span
										><span
											class="ml-2 capitalize font-semibold text-emerald-500"
											>Paid</span
										>
									</div>
								</td>
								<td
									class=""
									role="cell"
								>
									<span>10/08/2022</span>
								</td>
								<td
									class=""
									role="cell"
								>
									Ron Vargas
								</td>
								<td
									class=""
									role="cell"
								>
									<span>$168.00</span>
								</td>
							</tr>
							<tr
								class=""
								role="row"
							>
								<td
									class=""
									role="cell"
								>
									<span
										class="cursor-pointer select-none font-semibold hover:text-blue-600"
										>#95423</span
									>
								</td>
								<td
									class=""
									role="cell"
								>
									<div class="flex items-center">
										<span class="badge-dot bg-emerald-500"></span
										><span
											class="ml-2 capitalize font-semibold text-emerald-500"
											>Paid</span
										>
									</div>
								</td>
								<td
									class=""
									role="cell"
								>
									<span>30/07/2022</span>
								</td>
								<td
									class=""
									role="cell"
								>
									Carolyn Hanso
								</td>
								<td
									class=""
									role="cell"
								>
									<span>$523.00</span>
								</td>
							</tr>
							<tr
								class=""
								role="row"
							>
								<td
									class=""
									role="cell"
								>
									<span
										class="cursor-pointer select-none font-semibold hover:text-blue-600"
										>#92903</span
									>
								</td>
								<td
									class=""
									role="cell"
								>
									<div class="flex items-center">
										<span class="badge-dot bg-amber-500"></span
										><span class="ml-2 capitalize font-semibold text-amber-500"
											>Pending</span
										>
									</div>
								</td>
								<td
									class=""
									role="cell"
								>
									<span>18/07/2022</span>
								</td>
								<td
									class=""
									role="cell"
								>
									Gabriella May
								</td>
								<td
									class=""
									role="cell"
								>
									<span>$81.00</span>
								</td>
							</tr>
							<tr
								class=""
								role="row"
							>
								<td
									class=""
									role="cell"
								>
									<span
										class="cursor-pointer select-none font-semibold hover:text-blue-600"
										>#92627</span
									>
								</td>
								<td
									class=""
									role="cell"
								>
									<div class="flex items-center">
										<span class="badge-dot bg-red-500"></span
										><span class="ml-2 capitalize font-semibold text-red-500"
											>Failed</span
										>
									</div>
								</td>
								<td
									class=""
									role="cell"
								>
									<span>09/07/2022</span>
								</td>
								<td
									class=""
									role="cell"
								>
									Tara Fletcher
								</td>
								<td
									class=""
									role="cell"
								>
									<span>$279.00</span>
								</td>
							</tr>
							<tr
								class=""
								role="row"
							>
								<td
									class=""
									role="cell"
								>
									<span
										class="cursor-pointer select-none font-semibold hover:text-blue-600"
										>#89332</span
									>
								</td>
								<td
									class=""
									role="cell"
								>
									<div class="flex items-center">
										<span class="badge-dot bg-emerald-500"></span
										><span
											class="ml-2 capitalize font-semibold text-emerald-500"
											>Paid</span
										>
									</div>
								</td>
								<td
									class=""
									role="cell"
								>
									<span>02/06/2022</span>
								</td>
								<td
									class=""
									role="cell"
								>
									Eileen Horton
								</td>
								<td
									class=""
									role="cell"
								>
									<span>$597.00</span>
								</td>
							</tr>
							<tr
								class=""
								role="row"
							>
								<td
									class=""
									role="cell"
								>
									<span
										class="cursor-pointer select-none font-semibold hover:text-blue-600"
										>#86497</span
									>
								</td>
								<td
									class=""
									role="cell"
								>
									<div class="flex items-center">
										<span class="badge-dot bg-red-500"></span
										><span class="ml-2 capitalize font-semibold text-red-500"
											>Failed</span
										>
									</div>
								</td>
								<td
									class=""
									role="cell"
								>
									<span>18/03/2022</span>
								</td>
								<td
									class=""
									role="cell"
								>
									Lloyd Obrien
								</td>
								<td
									class=""
									role="cell"
								>
									<span>$189.00</span>
								</td>
							</tr>
							<tr
								class=""
								role="row"
							>
								<td
									class=""
									role="cell"
								>
									<span
										class="cursor-pointer select-none font-semibold hover:text-blue-600"
										>#86212</span
									>
								</td>
								<td
									class=""
									role="cell"
								>
									<div class="flex items-center">
										<span class="badge-dot bg-emerald-500"></span
										><span
											class="ml-2 capitalize font-semibold text-emerald-500"
											>Paid</span
										>
									</div>
								</td>
								<td
									class=""
									role="cell"
								>
									<span>09/03/2022</span>
								</td>
								<td
									class=""
									role="cell"
								>
									Tara Fletcher
								</td>
								<td
									class=""
									role="cell"
								>
									<span>$672.00</span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="card card-border">
			<div class="card-body">
				<div class="flex items-center justify-between mb-4">
					<h4>ERC20 Tokens Earned</h4>
				</div>
				<div class="overflow-x-auto">
					<table
						class="table-default table-hover"
						role="table"
					>
						<thead class="!bg-transparent">
							<tr
								class=""
								role="row"
							>
								<th
									class=""
									colspan="1"
									role="columnheader"
								>
									Token
								</th>
								<th
									class=""
									colspan="1"
									role="columnheader"
								>
									Amount
								</th>
							</tr>
						</thead>
						<tbody
							class=""
							role="rowgroup"
						>
							<tr
								class=""
								role="row"
							>
								<td
									class=""
									role="cell"
								>
									<div class="flex items-center gap-2">
										<span class="avatar avatar-rounded-sm avatar-md"
											><img
												class="avatar-img avatar-rounded-sm"
												src="/img/products/product-1.jpg"
												loading="lazy" /></span
										><span class="font-semibold"
											>Luminaire Giotto Headphones</span
										>
									</div>
								</td>
								<td
									class=""
									role="cell"
								>
									252
								</td>
							</tr>
							<tr
								class=""
								role="row"
							>
								<td
									class=""
									role="cell"
								>
									<div class="flex items-center gap-2">
										<span class="avatar avatar-rounded-sm avatar-md"
											><img
												class="avatar-img avatar-rounded-sm"
												src="/img/products/product-3.jpg"
												loading="lazy" /></span
										><span class="font-semibold">Black Sneaker</span>
									</div>
								</td>
								<td
									class=""
									role="cell"
								>
									186
								</td>
							</tr>
							<tr
								class=""
								role="row"
							>
								<td
									class=""
									role="cell"
								>
									<div class="flex items-center gap-2">
										<span class="avatar avatar-rounded-sm avatar-md"
											><img
												class="avatar-img avatar-rounded-sm"
												src="/img/products/product-4.jpg"
												loading="lazy" /></span
										><span class="font-semibold">Gray Hoodies</span>
									</div>
								</td>
								<td
									class=""
									role="cell"
								>
									166
								</td>
							</tr>
							<tr
								class=""
								role="row"
							>
								<td
									class=""
									role="cell"
								>
									<div class="flex items-center gap-2">
										<span class="avatar avatar-rounded-sm avatar-md"
											><img
												class="avatar-img avatar-rounded-sm"
												src="/img/products/product-5.jpg"
												loading="lazy" /></span
										><span class="font-semibold">Blue Backpack</span>
									</div>
								</td>
								<td
									class=""
									role="cell"
								>
									93
								</td>
							</tr>
							<tr
								class=""
								role="row"
							>
								<td
									class=""
									role="cell"
								>
									<div class="flex items-center gap-2">
										<span class="avatar avatar-rounded-sm avatar-md"
											><img
												class="avatar-img avatar-rounded-sm"
												src="/img/products/product-7.jpg"
												loading="lazy" /></span
										><span class="font-semibold">Strip Analog Watch</span>
									</div>
								</td>
								<td
									class=""
									role="cell"
								>
									81
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</template>
