package main

import (
	"fmt"
	"html/template"
	"log"
	"net/http"
	"strconv"
	"web/minibtc"
//	"io/ioutil"
//	"strings"
)


type TxInfo struct {
	To      string
	CostStr string
	Msg     string
}

func checkErr(err error) {
	if err != nil {
		panic(err)
	}
}
func logRequest(r *http.Request) {
	r.ParseForm()
	log.Println(r.Host, r.URL.String(), r.Method)
	fmt.Println(r.Form)
	fmt.Println()
}
func index(w http.ResponseWriter, r *http.Request) {
	logRequest(r)
	t, err := template.ParseFiles("index.html")
	checkErr(err)
	t.Execute(w, nil)
}
func fillTxInfo(w http.ResponseWriter, r *http.Request) {
	logRequest(r)
	var txInfo TxInfo
	txInfo.To = r.Form.Get("to")
	txInfo.CostStr = r.Form.Get("cost")
	txInfo.Msg = r.Form.Get("msg")
	t, err := template.ParseFiles("fillTxInfo.html")
	checkErr(err)
	t.Execute(w, txInfo)
}

func validateTx(w http.ResponseWriter, r *http.Request) {
	defer func() {
		if p := recover(); p != nil {
			fmt.Fprintln(w, 0)
		}
	}()
	logRequest(r)
	to := r.Form.Get("to")
	costStr := r.Form.Get("cost")
	value, err := strconv.Atoi(costStr)
	checkErr(err)
	msg := r.Form.Get("msg")
	txid := r.Form.Get("txid")
	var model minibtc.Model
	b := model.ValidateTx(to, value, msg, txid)
	fmt.Fprintln(w, b)
}
func createTx(w http.ResponseWriter, r *http.Request) {
	defer func() {
		if p := recover(); p != nil {
			fmt.Fprintln(w, "{\"txid\":\"-1\"}")
		}
	}()
	logRequest(r)
	if r.Method != "POST" {
		panic("method error")
	}
	var model minibtc.Model
	from := r.PostForm.Get("from")
	to := r.PostForm.Get("to")
	if from == to || (from == "ADGKJN2DH5OZ7EAU2WVS4RP3DPFH5U57MBW4LE6GRFORAVQBKEP7MD5TPWXFFNJTRYA6JLENJKRFHU3XDIGLPXXI4PTORDT3YSESCRWKLMQQRNQ=" || to == "ADGKJN2DH5OZ7EAU2WVS4RP3DPFH5U57MBW4LE6GRFORAVQBKEP7MD5TPWXFFNJTRYA6JLENJKRFHU3XDIGLPXXI4PTORDT3YSESCRWKLMQQRNQ="){
		commodity_id := r.PostForm.Get("commodity_id")
		msg := r.PostForm.Get("msg")
		privKey := r.PostForm.Get("privkey")
		value := 0
		if commodity_id == "0"{
			value=10
		}else{
			value=100
		}
		result := model.SendTx(from, to, value, msg, privKey)
		//println(model.GetBalance(to))
		fmt.Fprintln(w, "{\"txid\":\""+result+"\"}")
	}else{
		fmt.Fprintln(w, "{\"txid\":\"-1\"}")
	}
}

func getBalance(w http.ResponseWriter, r *http.Request) {
	defer func() {
		if p := recover(); p != nil {
			fmt.Fprintln(w, "-1")
		}
	}()
	logRequest(r)
	if r.Method != "GET" {
		panic("method error")
	}
	addr := r.Form.Get("addr")
	if len(addr) == 0 {
		t, err := template.ParseFiles("getBalance.html")
		checkErr(err)
		t.Execute(w, nil)
		return
	}
	var model minibtc.Model
	balance := model.GetBalance(addr)
	fmt.Fprintln(w, balance)
}
func createWallet(w http.ResponseWriter, r *http.Request) {
	var model minibtc.Model
	var walletStr = model.CreateWallet()
	fmt.Fprintln(w, walletStr)
}
func main() {
	var model minibtc.Model
	model.Init()
	http.HandleFunc("/", index)
	http.HandleFunc("/fillTxInfo", fillTxInfo)
	http.HandleFunc("/validateTx", validateTx)
	http.HandleFunc("/createTx", createTx)
	http.HandleFunc("/getBalance", getBalance)
	http.HandleFunc("/createWallet", createWallet)
	http.ListenAndServe(":8081", nil)
}

