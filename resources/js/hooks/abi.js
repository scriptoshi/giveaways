export const abiHasFunction = (abi, functionName) => {
    return !!abi.find(c => c.name === functionName && c.type === 'function');
};

export const arrayToAbiTupleObject = (abi, functionName, results) => {
    const funcAbi = abi.find(c => c.name === functionName && c.type === 'function' && c.stateMutability === 'view');
    if (!funcAbi) return results;
    const names = funcAbi.outputs.map(o => o.name);
    return Object.fromEntries(names.map((key, index) => [key, results[index]]));
};