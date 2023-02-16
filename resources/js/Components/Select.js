import React, { useEffect, useRef } from 'react';

export default function Select({
    name,
    value,
    className,
    autoComplete,
    required,
    isFocused,
    handleChange,
    values
}) {
    const input = useRef();

    useEffect(() => {
        if (isFocused) {
            input.current.focus();
        }
    }, []);

    return (
        <div className="flex flex-col items-start">
            <select ref={input}
                id={name} name={name} value={value} onChange={handleChange} className="text-left bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value={0}>انتخاب کنید</option>
                {
                    values.map((item, index) => (
                        <option key={index} value={item.value}>{item.title}</option>
                    ))
                }
            </select>
        </div>
    );
}
