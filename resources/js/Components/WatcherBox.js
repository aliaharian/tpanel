const WatcherBox = (props) => {
    return (
        <div className={`w-full flex items-start flex-col mb-9 ${props.className}`}>

            <h3 className="mb-2 mr-2 font-bold text-sm text-slate-600">{props.title}</h3>
            <div className="border w-full p-10 shadow-md rounded-lg">
                {props.children}
            </div>

        </div>
    )

}
export default WatcherBox;