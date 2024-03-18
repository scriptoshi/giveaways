
// shorten the checksummed version of the input address to have 0x + 4 characters at start and end
// returns the checksummed address if the address is valid, otherwise returns false
import { ContractFunctionRevertedError, getAddress } from 'viem';

export function shortenAddress(address, chars = 4) {
    if (!address) return null;
    if (address?.length <= chars) return address;
    return `${address.substring(0, parseInt(chars) + 2)}...${address.substring(
        42 - chars,
    )}`;
}
// shorten the checksummed version of the input address to have 0x + 4 characters at start and end

export function truncateTx(fullStr = "", strLen = 33, separator = "...") {
    if (fullStr?.length <= strLen) return fullStr;
    const sepLen = separator.length;
    const charsToShow = strLen - sepLen;
    const frontChars = Math.ceil(charsToShow / 2);
    const backChars = Math.floor(charsToShow / 2);
    return (
        fullStr.substring(0, frontChars + 3) +
        separator +
        fullStr.substring(fullStr.length - backChars - 3)
    );
}
export function isAddress(value) {
    try {
        return getAddress(value);
    } catch {
        return null;
    }
}

export const camelToTitle = (camel) => {
    return camel
        .replace(/[0-9]{2,}/g, match => ` ${match} `)
        .replace(/[^A-Z0-9][A-Z]/g, match => `${match[0]} ${match[1]}`)
        .replace(/[A-Z][A-Z][^A-Z0-9]/g, match => `${match[0]} ${match[1]}${match[2]}`)
        .replace(/[ ]{2,}/g, match => ' ')
        .replace(/\s./g, match => match.toUpperCase())
        .replace(/^./, match => match.toUpperCase())
        .trim();
};

export const getError = (err) => {
    const revertError = err.walk(err => err instanceof ContractFunctionRevertedError);
    if (revertError instanceof ContractFunctionRevertedError) {
        const errorName = revertError.data?.errorName ?? '';

        return camelToTitle(errorName);
    }
    return err.shortMessage;
};

