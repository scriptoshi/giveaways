
export const formatMoney = (
    money,
    locale = 'en',
    currencyCode = 'USD',
    subunitsValue = false,
    subunitsToUnit = 1,
    hideSubunits = false,
    supplementalPrecision = 0
) => {
    const ret = 0;
    let value = parseFloat(money);
    if (Number.isFinite(value)) {
        try {
            let numFormat = new Intl.NumberFormat(locale, { style: 'currency', currencyDisplay: 'code', currency: currencyCode });
            const options = numFormat.resolvedOptions();
            if (subunitsToUnit > 1) {
                value = value / subunitsToUnit;
            }
            else if (subunitsValue === true) {
                value = value / 10 ** options.minimumFractionDigits;
            }
            if (hideSubunits === true) {
                numFormat = new Intl.NumberFormat(locale, { style: 'currency', currencyDisplay: 'code', currency: currencyCode, minimumFractionDigits: 0, maximumFractionDigits: 0 });
            }
            else if (supplementalPrecision > 0) {
                numFormat = new Intl.NumberFormat(locale, {
                    style: 'currency',
                    currency: currencyCode,
                    currencyDisplay: 'code',
                    minimumFractionDigits: options.minimumFractionDigits + supplementalPrecision,
                    maximumFractionDigits: options.maximumFractionDigits + supplementalPrecision
                });
            }
            return numFormat.format(value).replace(currencyCode, "");
        }
        catch (err) {
            return err.message;
        }
    }
    return ret;
};



