import React from "react";

import { CKEditor } from "@ckeditor/ckeditor5-react";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";

const RichText = ({ value, handleChange }) => {
    return (
        <div className="App">
            <CKEditor
                editor={ClassicEditor}
                data={value}
                onReady={(editor) => {
                    // You can store the "editor" and use when it is needed.
                    console.log("Editor is ready to use!", editor);
                }}
                onChange={(event, editor) => {
                    const data = editor.getData();
                    handleChange(data);
                    console.log({ event, editor, data });
                }}
                onBlur={(event, editor) => {
                    // console.log( 'Blur.', editor );
                }}
                onFocus={(event, editor) => {
                    // console.log( 'Focus.', editor );
                }}
            />
        </div>
    );
};

export default RichText;
