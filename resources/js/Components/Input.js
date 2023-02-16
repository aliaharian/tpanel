import React, { useEffect, useRef } from "react";
import Num2persian from "num2persian";

export default function Input({
    type = "text",
    name = null,
    value = null,
    className,
    autoComplete = false,
    required = false,
    isFocused = false,
    handleChange = () => {},
    readonly,
    percentOrPrice = false,
}) {
    const input = useRef();
    const formatter = (value) => {};

    useEffect(() => {
        if (isFocused) {
            input.current.focus();
        }
    }, []);

    // console.log("value", value);
    //render value seperate 3 by 3 if type is currency
    const renderValue = (val) => {
        if (type === "currency") {
            if (val) {
                //number format with domma
                var tst = val.replaceAll(",", "");
                var str = tst.toString().split(".");
                if (str[0].length >= 5) {
                    str[0] = str[0].replace(/(\d)(?=(\d{3})+$)/g, "$1,");
                }
                if (str[1] && str[1].length >= 5) {
                    str[1] = str[1].replace(/(\d{3})/g, "$1 ");
                }
                return str.join(".");
            }
        }
        return value;
    };

    // console.log(percentOrPrice)
    return (
        <div className="flex flex-col items-start">
            <input
                type={type == "currency" ? "text" : type}
                name={name}
                direction="ltr"
                style={{ direction: "ltr" }}
                value={renderValue(value)}
                readOnly={readonly}
                className={
                    `border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm ` +
                    className
                }
                ref={input}
                autoComplete={autoComplete}
                required={required}
                onChange={(e) => {
                    handleChange(e, type);
                }}
            />
            {type === "currency" && value && (
                <p className="text-sm">
                    {value.num2persian()}{" "}
                    {percentOrPrice
                        ? value.length > 2
                            ? "ریال"
                            : "درصد"
                        : "ریال"}
                </p>
            )}
        </div>
    );
}
