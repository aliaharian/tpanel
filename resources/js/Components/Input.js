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
    var persianNumbers = [
            /۰/g,
            /۱/g,
            /۲/g,
            /۳/g,
            /۴/g,
            /۵/g,
            /۶/g,
            /۷/g,
            /۸/g,
            /۹/g,
        ],
        arabicNumbers = [
            /٠/g,
            /١/g,
            /٢/g,
            /٣/g,
            /٤/g,
            /٥/g,
            /٦/g,
            /٧/g,
            /٨/g,
            /٩/g,
        ],
        fixNumbers = function (str) {
            if (typeof str === "string") {
                for (var i = 0; i < 10; i++) {
                    str = str
                        .replace(persianNumbers[i], i)
                        .replace(arabicNumbers[i], i);
                }
            }
            return str;
        };

    // console.log("value", value);
    //render value seperate 3 by 3 if type is currency
    const renderValue = (val) => {
        if (type === "currency") {
            if (val) {
                //ignore anything except digits and
                val = val.toString().replace(/[^۰۱۲۳۴۵۶۷۸۹0-9]/g, "");
                let val2 = fixNumbers(val);
                //number format with domma
                var tst = val2.replaceAll(",", "");
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
        if (type == "number") {
            if (val) {
                //ignore anything except digits and
                val = val.toString().replace(/[^۰۱۲۳۴۵۶۷۸۹0-9]/g, "");
                let val2 = fixNumbers(val);
                console.log("val", val2);

                //number format with domma
                var tst = val2.replaceAll(",", "");
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
                type={
                    type == "currency"
                        ? "text"
                        : type == "number"
                        ? "text"
                        : type
                }
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
                    handleChange(
                        {
                            ...e,
                            target: {
                                ...e.target,
                                name: e.target.name,
                                value:
                                    type == "currency" || type == "number"
                                        ? fixNumbers(
                                              e.target.value
                                                  .toString()
                                                  .replace(
                                                      /[^۰۱۲۳۴۵۶۷۸۹0-9]/g,
                                                      ""
                                                  )
                                          )
                                        : fixNumbers(e.target.value),
                            },
                        },
                        type
                    );
                }}
            />
            {type === "currency" && value && (
                <p className="text-sm">
                    {fixNumbers(value).num2persian()}{" "}
                    {percentOrPrice
                        ? value.length > 2
                            ? "تومان"
                            : "درصد"
                        : "تومان"}
                </p>
            )}
        </div>
    );
}
