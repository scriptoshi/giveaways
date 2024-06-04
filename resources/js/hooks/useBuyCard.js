import { reactive, watch } from "vue";

import { DateTime } from "luxon";
import { useAccount, useChainId, usePublicClient } from "use-wagmi";
import { formatEther, zeroAddress } from "viem";

import { isAddress } from "@/Wagmi/utils/utils";

export const useBuyCardInfo = (abi, contract) => {
    const { address } = useAccount();
    const chainId = useChainId();
    const publicClient = usePublicClient();
    const sale = reactive({
        settings: {},
        endDate: DateTime.now().plus({ days: 10 }).toUnixInteger(),
        ended: false,
        tokenomics: {},
        tokens: 0,
        total: 0n,
        available: 0,
        count: 0,
        teamHasClaimedTokens: false,
        pool: zeroAddress,
        tokenId: 0,
        loading: false,
        contributions: 0,
        claimTime: DateTime.now().plus({ days: 10 }).toUnixInteger(),
        claimDate: DateTime.now().plus({ days: 10 }).toLocaleString(DateTime.DATETIME_MED),
        update: async () => null
    });

    const loadInfo = async (address) => {
        sale.loading = true;
        if (isAddress(address)) {
            sale.total = await publicClient.value.getBalance({
                address: contract
            });
        }

        sale.totalFormatted = formatEther(sale.total);
        sale.available = sale.total > 0 ? formatEther((sale.total * 85n) / 100n) : 0;
        const ctr = { abi, address: contract };
        const response = await publicClient.value.multicall({
            contracts: [
                { ...ctr, functionName: "settings" },
                { ...ctr, functionName: "tokenomics" },
                { ...ctr, functionName: "teamHasClaimedTokens" },
                { ...ctr, functionName: "pool" },
                { ...ctr, functionName: "tokenId" },
                { ...ctr, functionName: "count" },
                ...(address
                    ? [
                        { ...ctr, functionName: "contributions", args: [address] },
                        { ...ctr, functionName: "claims", args: [address] },
                        { ...ctr, functionName: "claimDate", args: [address] },
                    ]
                    : []),
            ],
        });
        console.log(response);
        if (response[0].status === "success") {

            sale.settings = response[0].result;
            sale.endDate = response[0].result.endTime;
            sale.tokenomics = response[1].status === "success" ? response[1].result : {};
            sale.teamHasClaimedTokens = response[2].status === "success" ? response[2].result : false;
            sale.pool = response[3].status === "success" ? response[3].result : sale.pool;
            sale.tokenId = response[4].status === "success" ? response[4].result : sale.tokenId;
            sale.count = response[5].status === "success" ? response[5].result : sale.count;
            sale.contributions =
                response[6]?.status === "success"
                    ? formatEther(response[6].result)
                    : sale.contributions;
            if (response[6]?.status === "success" && response[6].result > 0n && sale.total > 0n) {
                const av = (sale.total * 85n) / 100n;
                console.log(response[6]?.status, sale.tokenomics.membersAllocation, av);
                const tkns = (response[6]?.result * sale.tokenomics.membersAllocation) / av;
                sale.tokens = formatEther(tkns);
            }
            sale.claims = response[7]?.status === "success"
                ? formatEther(response[7].result)
                : sale.claims;
            sale.claimTime = response[8]?.status === "success" && response[8]?.result > 0n
                ? response[8]?.result
                : sale.claimDate;

            sale.claimDate = response[8]?.status === "success" && response[8]?.result > 0n
                ? DateTime.fromSeconds(parseInt(response[8].result)).toLocaleString(DateTime.DATETIME_MED)
                : sale.claimDate;
            sale.ended = DateTime.fromSeconds(parseInt(sale.endDate)) < DateTime.now();
        }
        sale.loading = false;
    };
    watch(
        [chainId, address],
        ([chainId, address]) => {
            if (chainId) loadInfo(address);
        },
        { immediate: true },
    );
    sale.update = loadInfo;
    return sale;
};