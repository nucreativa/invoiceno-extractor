<?php

namespace Nucreativa\InvoiceNoExtractor;

final class Extractor
{
    public function extract($sourceDesc): array
    {
        $invoices = [];

        $sourceDesc = preg_replace('/\s+/', '', $sourceDesc);
        $re = '/C\d{2}-\S+/m'; // Get all words after find the invoice format
        preg_match_all($re, $sourceDesc, $matches, PREG_SET_ORDER, 0);
        foreach ($matches as $match) {

            $re2 = '/C\d{2}-\d{6}/m'; // Single invoice with prefix
            preg_match_all($re2, $match[0], $matches2, PREG_SET_ORDER, 0);
            foreach ($matches2 as $match2) {
                $invoice = $match2[0];
                $invoices[] = $invoice;
            }

            $m = explode('/', $match[0]); // Remove /
            $subsets = explode(',', $m[0]);
            $prefix = $suffix = '';
            foreach ($subsets as $subset) {
                $string = $subset;
                if (strlen($string) >= 10) {
                    $prefix = substr($string, 0, 4);
                    $suffix = substr($string, 4, 6);
                } elseif (strlen($string) <= 6) {
                    $l = strlen($string);
                    if (strpos($string, "_") > 1) {
                        $x = explode("_", $string);
                        $len1 = strlen($x[0]);
                        $len2 = strlen($x[1]);
                        $num1 = (int) substr($x[0], -$len2);
                        $num2 = (int) $x[1];
                        $rng = range($num1, $num2);
                        foreach ($rng as $rn) {
                            $invoice = $prefix . substr_replace($suffix, str_pad($rn, $len2, 0, STR_PAD_LEFT), -$len2, $len2);
                            $invoices[] = $invoice;
                        }
                    } else {
                        $string = $prefix . substr_replace($suffix, $string, -$l, $l);
                    }
                }

                $re2 = "/C\d{2}-\d{6}$/m"; // Single invoice with prefix
                preg_match_all($re2, $string, $matches2, PREG_SET_ORDER, 0);
                foreach ($matches2 as $match2) {
                    $invoice = $match2[0];
                    $invoices[] = $invoice;
                }

                $re2b = "/^\d{6}$/m"; // Single invoice without prefix
                preg_match_all($re2b, $string, $matches2b, PREG_SET_ORDER, 0);
                foreach ($matches2b as $match2b) {
                    $invoice = $prefix . $match2b[0];
                    $invoices[] = $invoice;
                }

                $re3 = '/C\d{2}-\d{6}_\d+/m'; // Multi invoice with prefix
                preg_match_all($re3, $string, $matches3, PREG_SET_ORDER, 0);
                foreach ($matches3 as $match3) {
                    $x = explode("_", $match3[0]);
                    $len1 = strlen($x[0]);
                    $len2 = strlen($x[1]);
                    $num1 = (int) substr($x[0], -$len2);
                    $num2 = (int) $x[1];
                    $rng = range($num1, $num2);
                    foreach ($rng as $rn) {
                        $invoice = substr_replace($x[0], str_pad($rn, $len2, 0, STR_PAD_LEFT), -$len2, $len2);
                        $invoices[] = $invoice;
                    }
                }

                $re4 = '/\d{6}_\d+/m'; // Multi invoice without prefix
                preg_match_all($re4, $string, $matches4, PREG_SET_ORDER, 0);
                foreach ($matches4 as $match4) {
                    $x = explode("_", $match4[0]);
                    $len1 = strlen($x[0]);
                    $len2 = strlen($x[1]);
                    $num1 = (int) substr($x[0], -$len2);
                    $num2 = (int) $x[1];
                    $rng = range($num1, $num2);
                    foreach ($rng as $rn) {
                        $invoice = $prefix . substr_replace($x[0], str_pad($rn, $len2, 0, STR_PAD_LEFT), -$len2, $len2);
                        $invoices[] = $invoice;
                    }
                }
            }
        }

        $invoices = array_unique($invoices);
        sort($invoices);

        return $invoices;
    }
}
