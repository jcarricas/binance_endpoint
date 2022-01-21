# binance_endpoint PoC
A proof of concept to test the creation of a service that acts as a "rely" of Binance API.

The service gets the spot price from Binance API:
https://api.binance.com/api/v3/ticker/price

Capability of managing Binance API errors and limits, and the use of a disc cache to improve response time and reduce calls to Binance
